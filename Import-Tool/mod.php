<?php 
	require_once 'controller.php';	
	class mod
	{
		function __construct()
 	    {
     		$ctrl = new controller();
     		$action = isset($_GET['action']) ? $_GET['action']:null;
     		switch ($action) {
     		case 'upload':
                    $pathToXML=$ctrl->upload();
                    $pathToFolder=$ctrl->createFolder();
                    $ctrl->createPages($pathToFolder, $pathToXML);
                    $ctrl->zipFile($pathToFolder);
                    $ctrl->dlZip($pathToFolder);
                    break;
               default:
                    $ctrl->getAff();
               	break;
     		}
    	}
	}
?>