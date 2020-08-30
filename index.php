<?php
session_start();

require_once("include/fct.inc.php");
require_once("include/class.pdogsb.inc.php");

$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();

# si pas d'action demandée ou n'est pas connecté
if(!isset($_REQUEST['uc']) || !$estConnecte){
    $uc = 'connexion';
}else{
	$uc = $_REQUEST['uc'];
}

include("vues/v_entete.php") ;

# selon action demandée
switch($uc){
	case 'connexion':{
		include("controleurs/c_connexion.php");break;
	}
	case 'gererFrais' :{
		include("controleurs/c_gererFrais.php");break;
	}
	case 'etatFrais' :{
		include("controleurs/c_etatFrais.php");break; 
	}
}

include("vues/v_pied.php") ;
