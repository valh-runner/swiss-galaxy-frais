<?php
require_once("include/class.pdogsb.inc.php");

class Connexion_controller extends Controller
{

	function index()
	{
		if(!empty($_POST['login']) && !empty($_POST['mdp'])){
			$pdo = PdoGsb::getPdoGsb();
			$login = $_POST['login']; //TODO: secure user data input
			$mdp = $_POST['mdp']; //TODO: secure user data input
			$visiteur = $pdo->getInfosVisiteur($login, $mdp);
			
			if (!is_array($visiteur)) {
				ajouterErreur("Login ou mot de passe incorrect");
				include("vues/v_erreurs.php");
			} else {
				$id = $visiteur['id'];
				$nom =  $visiteur['nom'];
				$prenom = $visiteur['prenom'];
				connecter($id, $nom, $prenom); // mise en variables session idVisiteur, nom et prenom
				Controller::redirectSmart('connexion', 'accueil');
			}
		}

		include("vues/v_connexion.php");
	}

	function accueil(){
		include("vues/v_sommaire.php");
		include("vues/v_accueil.php");
	}

	function deconnexion(){
		deconnecter();
		Controller::redirectSmart('connexion', 'index');
	}
}
