<?php
require_once("include/class.pdogsb.inc.php");

class EtatFrais_controller extends Controller
{

	function index()
	{
		include("vues/v_sommaire.php");
		$idVisiteur = $_SESSION['idVisiteur'];

		$pdo = PdoGsb::getPdoGsb();
		$lesMois = $pdo->getLesMoisDisponibles($idVisiteur);

		//Si affichage d'un mois demandé
		if(empty($_REQUEST['lstMois'])){
			$lesCles = array_keys($lesMois);
			$moisASelectionner = $lesCles[0];// Afin de sélectionner par défaut le dernier mois dans la zone de liste
			include("vues/v_listeMois.php");
		}else{
			$leMois = $_REQUEST['lstMois'];
			$moisASelectionner = $leMois;
			$numAnnee = substr($leMois, 0, 4);
			$numMois = substr($leMois, 4, 2);
			include("vues/v_listeMois.php");

			$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
			$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
			$libEtat = $lesInfosFicheFrais['libEtat'];
			$montantValide = $lesInfosFicheFrais['montantValide'];
			$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
			$dateModif =  $lesInfosFicheFrais['dateModif'];
			$dateModif =  dateAnglaisVersFrancais($dateModif);
			include("vues/v_etatFrais.php");
		}
	}
}
