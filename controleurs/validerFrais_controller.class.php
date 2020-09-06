<?php
require_once("include/class.pdogsb.inc.php");

class validerFrais_controller extends Controller
{

	function index($idVisiteurAsked = null, $moisAsked = null)
	{
        Controller::accessOnly(['comptable']);
        
        //Si affichage d'une fiche de frais d'un visiteur demandé par formulaire
        if(!empty($_POST['lstVisiteur'])){
            $postIdVisiteur = sanitize($_POST['lstVisiteur']);
            $leMois = sanitize($_POST['lstMois']);
            Controller::redirectSmart('validerFrais', 'index', [$postIdVisiteur, $leMois]);
        }

        $pdo = PdoGsb::getPdoGsb();
        $lesVisiteurs = $pdo->getLesVisiteurs();
        
        $annee = (int) date("Y");
        $mois = (int) date("m");
        $douzesDerniersMois = array();
        for($i=1; $i<12; $i++){
            $mois--;
            if($mois == 0){
                $mois=12;
                $annee--;
            }
            if(strlen($mois) < 2){ $mois = '0'.$mois; }
            $douzesDerniersMois[] = [$annee, $mois];
        }

        $afficherFiche = false;
		//Si affichage d'une fiche de frais d'un visiteur demandé par url
		if(!empty($idVisiteurAsked) && !empty($moisAsked)){
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteurAsked, $moisAsked);
            //si le visiteur n'a pas de fiche pour le mois demandé
            if($lesInfosFicheFrais == null){
                ajouterErreur('Pas de fiche de frais pour ce visiteur ce mois');
            }else{
                $idEtat = $lesInfosFicheFrais['idEtat'];
                //si la fiche n'est pas à l'état cloturé
                if($idEtat != 'CL'){
                    ajouterErreur('Pas de fiche de frais à valider pour ce visiteur ce mois');
                }else{
                    $afficherFiche = true;

                    $numAnnee = substr($moisAsked, 0, 4);
                    $numMois = substr($moisAsked, 4, 2);
                    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteurAsked, $moisAsked);
                    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteurAsked, $moisAsked);

                    $this->set('lesFraisForfait', $lesFraisForfait);
                    $this->set('numMois', $numMois);
                    $this->set('numAnnee', $numAnnee);
                    $this->set('lesFraisHorsForfait', $lesFraisHorsForfait);
                }
            }
        }
        $this->set('idVisiteur', $idVisiteurAsked); //pour champ input hidden et autres
        $this->set('moisFiche', $moisAsked); //pour champ input hidden et autres
        $this->set('lesVisiteurs', $lesVisiteurs);
        $this->set('douzesDerniersMois', $douzesDerniersMois);
        $this->set('afficherFiche', $afficherFiche);
	}

	function validerMajFraisForfait()
	{
        Controller::accessOnly(['comptable']);

		$idVisiteur = sanitize($_POST['hiddenIdVisiteur']);
		$mois = sanitize($_POST['hiddenMoisFiche']);

		$pdo = PdoGsb::getPdoGsb();
		$lesFrais = sanitize($_POST['lesFrais']);
		if (lesQteFraisValides($lesFrais)) {
			$pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
			ajouterInfo("Les modifications ont été prise en compte");
		} else {
			ajouterErreur("Les valeurs des frais doivent être numériques");
        }
        
		Controller::redirectSmart('validerFrais', 'index', [$idVisiteur, $mois]);
	}

	function refuserFrais($idFrais, $idVisiteur, $mois)
	{
        Controller::accessOnly(['comptable']);

        $pdo = PdoGsb::getPdoGsb();
        $leFraisHF = $pdo->getFraisHorsForfait($idFrais); //récupération du frais hors forfait
        if(substr($leFraisHF['libelle'], 0, 7) == 'REFUSE:'){
            ajouterErreur("Refus du frais hors forfait impossible car déjà refusé");
        }else{
            //création nouveau frais dans fiche du mois actuel
            $pdo->creeNouveauFraisHorsForfait($idVisiteur, $mois, 'REFUSE:'.$leFraisHF['libelle'], $leFraisHF['date'], $leFraisHF['montant']);
            $pdo->supprimerFraisHorsForfait($idFrais);  //suppression ancien frais hors forfait
        }

		Controller::redirectSmart('validerFrais', 'index', [$idVisiteur, $mois]);
	}

	function reporterFrais($idFrais, $idVisiteur, $mois)
	{
        Controller::accessOnly(['comptable']);

        $pdo = PdoGsb::getPdoGsb();
        $leFraisHF = $pdo->getFraisHorsForfait($idFrais); //récupération du frais hors forfait
        $moisActuel = getMois(date("d/m/Y")); //mois actuel
        //si le visiteur n'a pas de fiche pour le mois actuel
        if($pdo->estPremierFraisMois($idVisiteur, $moisActuel)){
            $pdo->creeNouvellesLignesFrais($idVisiteur, $moisActuel);// création fiche
        }
        //création nouveau frais dans fiche du mois actuel
        $pdo->creeNouveauFraisHorsForfait($idVisiteur, $moisActuel, 'REPORTE:'.$leFraisHF['libelle'], $leFraisHF['date'], $leFraisHF['montant']);
        $pdo->supprimerFraisHorsForfait($idFrais);  //suppression ancien frais hors forfait

		Controller::redirectSmart('validerFrais', 'index', [$idVisiteur, $mois]);
	}

	function validerFiche($idVisiteur, $mois)
	{
        Controller::accessOnly(['comptable']);

        $pdo = PdoGsb::getPdoGsb();
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        if($lesInfosFicheFrais['idEtat'] == 'CL'){
            $pdo->majEtatFicheFrais($idVisiteur, $mois, 'VA');
            ajouterInfo("La validation de la fiche a été effectuée");
        }
        Controller::redirectSmart('validerFrais', 'index', [$idVisiteur, $mois]);
	}
}
