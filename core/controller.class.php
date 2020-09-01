<?php
class Controller{
	
	private $vars = array();
	public $noView = false;
	
	function __construct($urlAction, $urlParams){
        $deducedMethodName = lcfirst(implode(array_map('ucfirst', explode('_', $urlAction))));
		call_user_func_array(array($this, $deducedMethodName), $urlParams); //call of controller object method
	}
	
	function set($varname, $var){
		$this->vars[$varname] = $var;
	}
	
	static function redirect(array $url){
		header('HTTP/1.1 301 Moved Permanently');
		//header('Location: /mymvc/'.$url['page'].'html', 301);
		header('Location: '.URLROOT.$url['page'].'/'.$url['action']);
		exit();
	}
	
	static function redirectSmart(String $urlPage, String $urlAction){
		header('HTTP/1.1 301 Moved Permanently');
		//header('Location: /mymvc/'.$url['page'].'html', 301);
		header('Location: '.URLROOT.$urlPage.'/'.$urlAction);
		exit();
	}
	
}