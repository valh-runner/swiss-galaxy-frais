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
			$user = $pdo->getInfosVisiteur($login, $mdp);
			
			if (!is_array($user)) {
				ajouterErreur("Login ou mot de passe incorrect");
				include("vues/v_erreurs.php");
			} else {
				$id = $user['id'];
				$nom =  $user['nom'];
				$prenom = $user['prenom'];
				$role = $user['role'];
				connecter($id, $nom, $prenom, $role); // mise en variables session idVisiteur, nom et prenom
                Controller::redirectSmart('connexion', 'accueil');                
			}
		}
	}

	function accueil(){

        if($_SESSION['role'] == 'comptable'){
            //Si date supérieure au 10 du mois
            // cloture de toutes les fiches de frais non encore cloturées
        }
        
	}

	function deconnexion(){
		deconnecter();
		Controller::redirectSmart('connexion', 'index');
	}
}
