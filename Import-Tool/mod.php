<?php 
	require_once 'controller.php';	
	class mod
	{
		function __construct()
 	    {
     		$ctrl = new controller();
     		$action = isset($_GET['action']) ? $_GET['action']:null;
     		switch ($action) {
     		 case 'createPage':
          //initializes a session variable with a random token for the next step after the form processing
          if(!isset($_SESSION['token'])) $_SESSION['token']=random_int(0, 100);
     		  $ctrl->createPages();
          break;
     		 default:
          $ctrl->getAff();
     			break;
     		}
    	}
	}
?>