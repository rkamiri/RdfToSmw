<?php
	require 'view.php';
	require 'model.php';
	class controller{
		private $view;
		private $mod;

		public function __construct(){
			$this->view=new View();
			$this->mod=new Model();
		}

		public function getAff(){
			return $this->view->getBody();
		}
		
		public function createPages(){
			$pathToFolder=$this->mod->createFolder();
			$pathToFile=$this->createPage($pathToFolder);
			$this->dlPage($pathToFile);
		}
		public function createPage($path){
			return $this->mod->createPage($path);
		}

		public function dlPage($path){
			return $this->mod->dlPage($path);
		}
		/*public function sendMail(){
		    $this->mod->sendMail();
		    $this->vue->getBody();
		}*/
	}
?>