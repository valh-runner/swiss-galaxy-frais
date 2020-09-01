<?php
class Controller
{

	private $vars = array();

	function __construct($urlAction, $urlParams)
	{
		$deducedMethodName = lcfirst(implode(array_map('ucfirst', explode('_', $urlAction))));
		call_user_func_array(array($this, $deducedMethodName), $urlParams); //call of controller object method
		$this->callView($urlAction);
	}

	function set($varname, $var)
	{
		$this->vars[$varname] = $var;
	}

	function callView($view)
	{
		$deducedPage = strtolower(str_replace('_controller', '', get_class($this))); //voir si c mieux de r�cup directement $url['page']
		// $oView = new View($deducedPage, $view, $this->vars, $this->noView);
		$this->render($deducedPage, $view, $this->vars);
	}

	function render($page, $view, $vars = array())
	{
		extract($vars);

		$viewPath = 'vues/' . $page . '/' . $view . '.php';
		//if view exists
		if (is_file($viewPath)) {
			ob_start();
			require(ROOT . $viewPath);
			$content_for_layout = ob_get_clean();
		} else {
			$content_for_layout = 'NO VIEW';
		}

		require(ROOT . 'vues/common/template.php');
		exit(); //fin de la réponse
	}

	static function redirect(array $url)
	{
		header('HTTP/1.1 301 Moved Permanently');
		//header('Location: /mymvc/'.$url['page'].'html', 301);
		header('Location: ' . URLROOT . $url['page'] . '/' . $url['action']);
		exit();
	}

	static function redirectSmart(String $urlPage, String $urlAction)
	{
		header('HTTP/1.1 301 Moved Permanently');
		//header('Location: /mymvc/'.$url['page'].'html', 301);
		header('Location: ' . URLROOT . $urlPage . '/' . $urlAction);
		exit();
	}
}
