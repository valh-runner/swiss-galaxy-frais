<?php
require_once("include/class.pdogsb.inc.php");

class validerFrais_controller extends Controller
{

	function index()
	{
        Controller::accessOnly(['comptable']);
        
	}
}
