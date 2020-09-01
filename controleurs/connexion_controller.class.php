<?php
require_once("include/class.pdogsb.inc.php");

class Connexion_controller extends Controller
{

	function index()
	{
		include("vues/v_connexion.php");
	}

	function valideConnexion()
	{
		$pdo = PdoGsb::getPdoGsb();
		$login = $_POST['login'];
		$mdp = $_POST['mdp'];
		$visiteur = $pdo->getInfosVisiteur($login, $mdp);

		if (!is_array($visiteur)) {
			ajouterErreur("Login ou mot de passe incorrect");
			include("vues/v_erreurs.php");
			include("vues/v_connexion.php");
		} else {
			$id = $visiteur['id'];
			$nom =  $visiteur['nom'];
			$prenom = $visiteur['prenom'];
			connecter($id, $nom, $prenom); // mise en variables session idVisiteur, nom et prenom
			Controller::redirectSmart('connexion', 'accueil');
		}
	}

	function accueil(){
		include("vues/v_sommaire.php");
	}

	function deconnexion(){
		deconnecter();
		Controller::redirectSmart('connexion', 'index');
	}
}
