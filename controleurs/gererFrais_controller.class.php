<?php
require_once("include/class.pdogsb.inc.php");

class GererFrais_controller extends Controller
{

	function saisirFrais()
	{
		include("vues/v_sommaire.php");
		$idVisiteur = $_SESSION['idVisiteur'];
		$mois = getMois(date("d/m/Y"));
		$numAnnee = substr($mois, 0, 4); //useless??
		$numMois = substr($mois, 4, 2); //useless??

		$pdo = PdoGsb::getPdoGsb();
		if ($pdo->estPremierFraisMois($idVisiteur, $mois)) { // si pas encore de fiche de frais pour le mois en cours
			$pdo->creeNouvellesLignesFrais($idVisiteur, $mois); // création fiche de frais pour le mois en cours et cloture fiche précédente
		}

		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
		$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
		include("vues/v_listeFraisForfait.php");
		include("vues/v_listeFraisHorsForfait.php");
	}

	function validerMajFraisForfait()
	{
		include("vues/v_sommaire.php");
		$idVisiteur = $_SESSION['idVisiteur'];
		$mois = getMois(date("d/m/Y"));
		$numAnnee = substr($mois, 0, 4); //useless??
		$numMois = substr($mois, 4, 2); //useless??

		$pdo = PdoGsb::getPdoGsb();
		$lesFrais = $_REQUEST['lesFrais'];
		if (lesQteFraisValides($lesFrais)) {
			$pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
		} else {
			ajouterErreur("Les valeurs des frais doivent être numériques");
			include("vues/v_erreurs.php");
		}

		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
		$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
		include("vues/v_listeFraisForfait.php");
		include("vues/v_listeFraisHorsForfait.php");
	}

	function validerCreationFrais()
	{
		include("vues/v_sommaire.php");
		$idVisiteur = $_SESSION['idVisiteur'];
		$mois = getMois(date("d/m/Y"));
		$numAnnee = substr($mois, 0, 4); //useless??
		$numMois = substr($mois, 4, 2); //useless??

		$pdo = PdoGsb::getPdoGsb();
		$dateFrais = $_REQUEST['dateFrais'];
		$libelle = $_REQUEST['libelle'];
		$montant = $_REQUEST['montant'];
		echo $_REQUEST['dateFrais'];
		valideInfosFrais($dateFrais, $libelle, $montant);

		if (nbErreurs() != 0) {
			include("vues/v_erreurs.php");
			echo 'BAD';
		} else {
			$pdo->creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $dateFrais, $montant);
			
		}

		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
		$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
		include("vues/v_listeFraisForfait.php");
		include("vues/v_listeFraisHorsForfait.php");
	}

	function supprimerFrais($idFrais)
	{
		include("vues/v_sommaire.php");
		$idVisiteur = $_SESSION['idVisiteur'];
		$mois = getMois(date("d/m/Y"));
		$numAnnee = substr($mois, 0, 4); //useless??
		$numMois = substr($mois, 4, 2); //useless??

		$pdo = PdoGsb::getPdoGsb();
		// $idFrais = $_REQUEST['idFrais'];
		$pdo->supprimerFraisHorsForfait($idFrais);

		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
		$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
		include("vues/v_listeFraisForfait.php");
		include("vues/v_listeFraisHorsForfait.php");
	}
}
