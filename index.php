<?php

#PATHS

define('DS', '/');
define('URLROOT', 'http://'.$_SERVER['HTTP_HOST'].DS); //      http://alias/
define('ROOT', dirname($_SERVER['SCRIPT_FILENAME']).DS); //	F:/virtualhosts/alias/

#CORE

require_once('core/controller.class.php');
require_once("include/fct.inc.php");
require_once("include/class.pdogsb.inc.php");
session_start();

#DISPATCHER

// $urlParts = explode('/', strtolower($_GET['rewrite'])); //url explosion
$urlParts = explode('/', $_GET['rewrite']); //url explosion
foreach($urlParts as $urlPart){ //for each url part
    $urlPart = htmlspecialchars(trim($urlPart)); //sanitization
}
$url = array('page' => null, 'action' => null, 'params' => array()); //default link structuration
//asked link construction
if(count($urlParts) > 0){ $url['page'] = $urlParts[0]; }
if(count($urlParts) > 1){ $url['action'] = $urlParts[1]; }
if(count($urlParts) > 2){
	$params = array_slice($urlParts, 2);
    foreach($params as $param){
        if(!empty($param)){
            $url['params'][] = $param;
        }
    }
}

#ROUTER

$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();

//if page specified
if( !empty($url['page'])){
	
	$controllerPath = 'controleurs/'.$url['page'].'_controller.class.php';
	// if page exists
	if(is_file($controllerPath)){
		require_once($controllerPath); //load controller of page
		$controllerName = ucfirst($url['page']).'_controller';
		
		// if action specified
		if(!empty($url['action'])){
			$methodName = lcfirst(implode(array_map('ucfirst', explode('_', $url['action']))));
			// if action exists
			if(method_exists($controllerName, $methodName)){
				//if user logged
				if($estConnecte){
					$url = array('page'=>$url['page'], 'action'=>$url['action'], 'params'=>$url['params']); // Let pass
				}else{
					//if a connection action asked
					if($url['page']=='connexion' && $url['action'] == 'index'){
						$url = array('page'=>$url['page'], 'action'=>$url['action'], 'params'=>array()); // Let pass
					}else{
						Controller::redirectSmart('connexion', 'index');
					}
				}
			}else{
				Controller::redirectSmart($url['page'], 'index');
			}
		}else{
			Controller::redirectSmart($url['page'], 'index');
		}
	}else{
		Controller::redirectSmart('common', 'error404');
	}
} else { 
	//if user logged
	if($estConnecte){
		Controller::redirectSmart('connexion', 'accueil');
	}else{
		Controller::redirectSmart('connexion', 'index');
	}
}

#CONTROLLER INVOCATION

$oController = new $controllerName($url['action'], $url['params']); //controller instanciation
