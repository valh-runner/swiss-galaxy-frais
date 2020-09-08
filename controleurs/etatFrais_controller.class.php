<?php
require_once("include/class.pdogsb.inc.php");

class EtatFrais_controller extends Controller
{

	function index()
	{
        Controller::accessOnly(['visiteur']);
        
		$idVisiteur = $_SESSION['idUser'];
		$pdo = PdoGsb::getPdoGsb();
		$lesMois = $pdo->getLesMoisDisponibles($idVisiteur);

		//Si affichage d'un mois demandé
		if(empty($_POST['lstMois'])){
			$lesCles = array_keys($lesMois);
			$moisASelectionner = $lesCles[0];// Afin de sélectionner par défaut le dernier mois dans la zone de liste
		}else{
			$leMois = sanitize($_POST['lstMois']);
			$moisASelectionner = $leMois;
			$numAnnee = substr($leMois, 0, 4);
			$numMois = substr($leMois, 4, 2);

			$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
			$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
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
		}

		$this->set('lesMois', $lesMois);
		$this->set('moisASelectionner', $moisASelectionner);
	}
}
