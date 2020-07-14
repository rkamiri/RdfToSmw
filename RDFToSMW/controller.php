<?php
	require 'view.php';
	require 'model.php';
	class controller{
		private $view;
		private $model;

		public function __construct(){
			$this->view=new View();
			$this->model=new Model();
		}

		public function getAff(){
			return $this->view->getBody();
		}

		public function upload(){
			return $this->model->upload();
		}

		public function createFolder(){
			return $this->model->createFolder();
		}

		public function createPages($pathToFolder, $pathToXML){
			$this->model->createPages($pathToFolder, $pathToXML);
		}

		public function zipFile($pathToFolder){
			$this->model->zipFile($pathToFolder);
		}

		public function dlZip($pathToFolder){
			$this->model->dlZip("PageStorage/".$pathToFolder.".zip");
		}

	}
?>