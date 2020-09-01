<?php
require_once("include/class.pdogsb.inc.php");

class EtatFrais_controller extends Controller
{

	function selectionnerMois()
	{
		include("vues/v_sommaire.php");
		$idVisiteur = $_SESSION['idVisiteur'];

		$pdo = PdoGsb::getPdoGsb();
		$lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
		// Afin de sélectionner par défaut le dernier mois dans la zone de liste
		$lesCles = array_keys($lesMois);
		$moisASelectionner = $lesCles[0];
		include("vues/v_listeMois.php");
	}

	function voirEtatFrais()
	{
		include("vues/v_sommaire.php");
		$idVisiteur = $_SESSION['idVisiteur'];

		$pdo = PdoGsb::getPdoGsb();
		$leMois = $_REQUEST['lstMois'];
		$lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
		$moisASelectionner = $leMois;
		include("vues/v_listeMois.php");
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
		$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
		$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
		$numAnnee = substr($leMois, 0, 4);
		$numMois = substr($leMois, 4, 2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
		include("vues/v_etatFrais.php");
	}
}
