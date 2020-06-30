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
                    if(isset($_POST['viki'])){
                         $_SESSION['viki']='true';
                    }else  $_SESSION['viki']='false';

                    if(!isset($_POST['url'])){
                         $_SESSION['url']=$_POST['url'];
                         $_SESSION['url']=substr($_SESSION['url'], 0, strpos($_SESSION['url'], "index"));
                         $_SESSION['url']=$_SESSION['url'].'index.php/';
                         echo $_SESSION['url'];
                    }
                    $_SESSION['token']=random_int(1, 1999);
                                       
                    $pathToXML=$ctrl->upload();
                    $pathToFolder=$ctrl->createFolder();
                    $ctrl->createPages($pathToFolder, $pathToXML);
                    $ctrl->zipFile($pathToFolder);
                    $ctrl->dlZip($pathToFolder);
                    $ctrl->removeAll($pathToFolder, $pathToXML);
                    http_redirect("index.php");
                    echo $_SESSION['viki'];
                    break;
               default:
                    $ctrl->getAff();
               	break;
     		}
    	}
	}
?>