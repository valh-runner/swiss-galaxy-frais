<?php
require_once("include/class.pdogsb.inc.php");

class Connexion_controller extends Controller
{

    function index()
    {
        if (!empty($_POST['login']) && !empty($_POST['mdp'])) {
            $pdo = PdoGsb::getPdoGsb();
            $login = sanitize($_POST['login']);
            $mdp = sanitize($_POST['mdp']);

            if (is_array($user = $pdo->getInfosVisiteur($login, $mdp))) {
                connecter($user['id'], $user['nom'], $user['prenom'], 'visiteur'); // mise en variables session idVisiteur, nom et prenom
                Controller::redirectSmart('connexion', 'accueil');
            }
            elseif (is_array($user = $pdo->getInfosComptable($login, $mdp))) {
                connecter($user['id'], $user['nom'], $user['prenom'], 'comptable'); // mise en variables session idComptable, nom et prenom
                Controller::redirectSmart('connexion', 'accueil');
            } else {
                ajouterErreur("Login ou mot de passe incorrect");
            }
        }
    }

    function accueil()
    {
        if ($_SESSION['role'] == 'comptable') {
            $jourDuMois = (int) date("d");
            //si jour actuel du mois est au moins le 10
            if ($jourDuMois >= 10) {
                $pdo = PdoGsb::getPdoGsb();
                $nbrFichesACloturer = $pdo->getNbrFichesACloturer();
                //si des fiches restent à cloturer
                if ($nbrFichesACloturer != 0) {
                    $pdo->cloturerFichesACloturer(); //cloture des fiches de frais non encore cloturées
                }
            }
        }
    }

    function deconnexion()
    {
        deconnecter();
        Controller::redirectSmart('connexion', 'index');
    }
}
