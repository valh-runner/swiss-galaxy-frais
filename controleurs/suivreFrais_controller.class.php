<?php
require_once("include/class.pdogsb.inc.php");

class suivreFrais_controller extends Controller
{

	function index()
	{
        Controller::accessOnly(['comptable']);
        
        $pdo = PdoGsb::getPdoGsb();

        $fichesClotureesEtValidees = $pdo->getFichesClotureesEtValidees();
        $this->set('fichesClotureesEtValidees', $fichesClotureesEtValidees);

		//Si affichage d'une fiche de frais d'un visiteur demandé
		if(!empty($_POST['lstFiche'])){

            $ficheDemandee = explode('-', sanitize($_POST['lstFiche']));
            $idVisiteur = $ficheDemandee[0];
            $leMois = $ficheDemandee[1];

            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);

            if($lesInfosFicheFrais == null){
                ajouterErreur('Pas de fiche de frais pour ce visiteur ce mois');
                $this->set('ficheExiste', false);
            }else{
                $this->set('ficheExiste', true);

                $numAnnee = substr($leMois, 0, 4);
                $numMois = substr($leMois, 4, 2);

                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
                $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
                $libEtat = $lesInfosFicheFrais['libEtat'];
                $montantValide = $lesInfosFicheFrais['montantValide'];
                $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
                $dateModif =  $lesInfosFicheFrais['dateModif'];
                $dateModif =  dateAnglaisVersFrancais($dateModif);

                $this->set('lesFraisForfait', $lesFraisForfait);
                $this->set('numMois', $numMois);
                $this->set('numAnnee', $numAnnee);

                $this->set('lesFraisHorsForfait', $lesFraisHorsForfait);

                $this->set('libEtat', $libEtat);
                $this->set('montantValide', $montantValide);
                $this->set('nbJustificatifs', $nbJustificatifs);
                $this->set('dateModif', $dateModif);


                $this->set('idEtat', $lesInfosFicheFrais['idEtat']);
                $this->set('idVisiteur', $idVisiteur);
                $this->set('mois', $leMois);
            }
		}        
	}

	function marquerCommeValidee($idVisiteur, $mois)
	{
        Controller::accessOnly(['comptable']);
        
        $pdo = PdoGsb::getPdoGsb();
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        if($lesInfosFicheFrais['idEtat'] == 'CL'){
            $pdo->majEtatFicheFrais($idVisiteur, $mois, 'VA');
            ajouterInfo("La fiche a bien été marquée comme validée");
        }
        Controller::redirectSmart('suivreFrais', 'index');        
	}

	function marquerCommeRemboursee($idVisiteur, $mois)
	{
        Controller::accessOnly(['comptable']);
        
        $pdo = PdoGsb::getPdoGsb();
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        if($lesInfosFicheFrais['idEtat'] == 'VA'){
            $pdo->majEtatFicheFrais($idVisiteur, $mois, 'RB');
            ajouterInfo("La fiche a bien été marquée comme remboursée");
        }
        Controller::redirectSmart('suivreFrais', 'index');        
	}
}
