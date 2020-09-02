<?php
require_once("include/class.pdogsb.inc.php");

class suivreFrais_controller extends Controller
{

	function index()
	{
        Controller::accessOnly(['comptable']);
		
	}
}
