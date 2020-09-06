<?php
require_once("include/class.pdogsb.inc.php");

class GererFrais_controller extends Controller
{

	function index()
	{
        Controller::accessOnly(['visiteur']);

		$idVisiteur = $_SESSION['idUser'];
		$mois = getMois(date("d/m/Y"));
		$pdo = PdoGsb::getPdoGsb();

		if ($pdo->estPremierFraisMois($idVisiteur, $mois)) { // si pas encore de fiche pour le mois actuel
			$pdo->creeNouvellesLignesFrais($idVisiteur, $mois); // création fiche et cloture fiche précédente
		}

		$numAnnee = substr($mois, 0, 4); //used by included v_listeFraisForfait.php
		$numMois = substr($mois, 4, 2); //used by included v_listeFraisForfait.php
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
		$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);

		$this->set('lesFraisForfait', $lesFraisForfait);
		$this->set('numMois', $numMois);
		$this->set('numAnnee', $numAnnee);

        $this->set('lesFraisHorsForfait', $lesFraisHorsForfait);
	}

	function validerMajFraisForfait()
	{
        Controller::accessOnly(['visiteur']);

		$idVisiteur = $_SESSION['idUser'];
		$mois = getMois(date("d/m/Y"));

		$pdo = PdoGsb::getPdoGsb();
		$lesFrais = sanitize($_POST['lesFrais']);
		if (lesQteFraisValides($lesFrais)) {
			$pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
		} else {
			ajouterErreur("Les valeurs des frais doivent être numériques");
		}

		Controller::redirectSmart('gererFrais', 'index');
	}

	function validerCreationFrais()
	{
        Controller::accessOnly(['visiteur']);
        
		$idVisiteur = $_SESSION['idUser'];
		$mois = getMois(date("d/m/Y"));

		$pdo = PdoGsb::getPdoGsb();
		$dateFrais = sanitize($_POST['dateFrais']);
		$libelle = sanitize($_POST['libelle']);
		$montant = sanitize($_POST['montant']);

		valideInfosFrais($dateFrais, $libelle, $montant);

		if (nbErreurs() == 0) {
			$pdo->creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $dateFrais, $montant);
		}

		Controller::redirectSmart('gererFrais', 'index');
	}

	function supprimerFrais($idFrais)
	{
		$pdo = PdoGsb::getPdoGsb();
		$pdo->supprimerFraisHorsForfait($idFrais);

		Controller::redirectSmart('gererFrais', 'index');
	}
}
