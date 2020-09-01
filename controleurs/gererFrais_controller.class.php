<?php
require_once("include/class.pdogsb.inc.php");

class GererFrais_controller extends Controller
{

	function index()
	{
		include("vues/v_sommaire.php");
		include("vues/v_erreurs.php");

		$idVisiteur = $_SESSION['idVisiteur'];
		$mois = getMois(date("d/m/Y"));
		$pdo = PdoGsb::getPdoGsb();

		if ($pdo->estPremierFraisMois($idVisiteur, $mois)) { // si pas encore de fiche de frais pour le mois en cours
			$pdo->creeNouvellesLignesFrais($idVisiteur, $mois); // création fiche de frais pour le mois en cours et cloture fiche précédente
		}

		$numAnnee = substr($mois, 0, 4); //used by included v_listeFraisForfait.php
		$numMois = substr($mois, 4, 2); //used by included v_listeFraisForfait.php
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
		$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
		include("vues/v_listeFraisForfait.php");
		include("vues/v_listeFraisHorsForfait.php");
	}

	function validerMajFraisForfait()
	{
		$idVisiteur = $_SESSION['idVisiteur'];
		$mois = getMois(date("d/m/Y"));

		$pdo = PdoGsb::getPdoGsb();
		$lesFrais = $_REQUEST['lesFrais'];
		if (lesQteFraisValides($lesFrais)) {
			$pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
		} else {
			ajouterErreur("Les valeurs des frais doivent être numériques");
		}

		Controller::redirectSmart('gererFrais', 'index');
	}

	function validerCreationFrais()
	{
		$idVisiteur = $_SESSION['idVisiteur'];
		$mois = getMois(date("d/m/Y"));

		$pdo = PdoGsb::getPdoGsb();
		$dateFrais = $_REQUEST['dateFrais'];
		$libelle = $_REQUEST['libelle'];
		$montant = $_REQUEST['montant'];

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
