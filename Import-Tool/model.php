<?php
     class Model{
          function __construct(){
          }
          function sendMail(){
            $mail= $_POST['mail'];
            $sujet = $_POST['sujet'];
            $message =  $_POST['message'];
            $a = "foliowave.enterprise@gmail.com";
            $entete = "From:" . $mail;
            ini_set( 'display_errors', 1 );
            error_reporting( E_ALL );
            mail($a,$sujet,$message, $entete);
            return true;
        }

        public function upload(){
         
          if(!empty($_FILES['uploaded_file']))
            {
              $path = "upload/";
              $path = $path . basename( $_FILES['uploaded_file']['name']);

              if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path)) {
                echo "The file ".  basename( $_FILES['uploaded_file']['name']). 
                " has been uploaded";
                return $path;
              } else{
                  echo "There was an error uploading the file, please try again!";
              }
            }
        }

         /*
         * Creer un dossier pour mettre tout dedans
         */
        public function createFolder(){
            $name = $_SESSION['token'];
            if(file_exists('PageStorage/'.$name)){
                while(file_exists('PageStorage/'.$name)){
                    $name= random_int(1, 1999).$name;
                }
            }
            mkdir('PageStorage/'.$name, 0777, true);
            return $name;
        }

        function createPages($pathToFolder, $pathToXML){
          $handle = fopen($pathToXML, 'r');
          $data = file_get_contents($pathToXML);
          $data = str_replace('  ', '', $data);
          $data = str_replace('<', '[', $data);
          $data = str_replace('>', ']', $data);
          file_put_contents($pathToXML, $data);
          $i=0;
          if ($handle)
          {
            //initializing all the variables needed to create the page and to keep in memory the strings of the old and new titles.
            $title;
            $oldTitle;
            $belongsTo="";
            $contains="";
            $content="";
            $currentType="";
            $fileOrga;
            while (!feof($handle)){
              $buffer = fgets($handle);
              
              if(!isset($fileOrga)){
                $temp = $this->checkFileOrganisation($buffer);
                if($temp!='continue'){
                  $fileOrga=$temp;
                }
              }

              if(isset($fileOrga)){
                if($fileOrga=="rdfWNames"){         
                  $currentType = $this->checktypeWName($buffer);
                  if($currentType=='title'){
                    if(!isset($title))$title=$this->get_string_between($buffer, '#', '"');
                    if($title!=$this->get_string_between($buffer, '#', '"')){
                      //echo  $title.$content.$contains.$belongsTo."</br>"; 
                      $pageArray = array(0=>$title, 1=>$content, 2=>$belongsTo);              
                      $this->createPage($pathToFolder, $pageArray);
                      $content="";
                      $belongsTo=""; 
                      $title=$this->get_string_between($buffer, '#', '"');
                    }
                  }

                  if($currentType=='content'){
                    $content=$content." contient ".$this->get_string_between($buffer, ']', '[/');
                  }

                  if($currentType=='belongsTo'){
                    $belongsTo=$belongsTo." is a subclass from ".$this->get_string_between($buffer, '#', '"');
                  }
                }
                    
                if($fileOrga=="rdfWONames"){
                  echo "habib";
                }
              }
             }
            fclose($handle);
          }    
        }

         function checkFileOrganisation($string){
          //replaces the colon in order to prevent any character conflict with the strpos function
          $string=str_replace(":","",$string);
          if(strpos($string, '[owlClass') || strpos($string, '[rdfsClass') || strpos($string, 'Class rdfabout=')){
            
            //If a # is spotted, it means that the class has a real name, hence rdfWName -> rdf with name
            if(strpos($string, '#')){
              return 'rdfWNames';
            }
            //if it doesnt contain a # it means that the class uses an alias 
            else{
              return 'rdfWONames';
            }
          }
          //the case that the line inspected isnt a class
          else 
            return 'continue';
        }


        //a function to check the type of the line of the RDF file, usefull to add content to a mediawiki page
        function checktypeWName($string){
          //title = [owlClass, [rdfsClass, Class rdfabout=; 
          $string=str_replace(":","",$string);
          $got = array(0=>'[owlClass', 1=>'[rdfsClass', 2=>'rdfssubClassOf ', 3=>'rdfscomment');
          $poss = array('title','title', 'belongsTo', 'content');
          
          foreach($got as $value){
            //$mystring = "The quick brown fox rdfs:subPropertyOf jumps over the lazy dog";
            $result= strpos($string, $value); 
            if( $result !== false){
              return $poss[array_search($value, $got)];
            } 
          }
        }  

        function get_string_between($string, $start, $end){
          $string = ' ' . $string;
          $ini = strpos($string, $start);
          if ($ini == 0) return '';
          $ini += strlen($start);
          $len = strpos($string, $end, $ini) - $ini;
          return substr($string, $ini, $len);
        }

        /*
         * Create a new SMW page in XML using the information of the form 
         * The file is stored insite the folder created by the system
         */

        function createPage($pathToFolder, $pageArray){

            $preview = substr($pageArray[1].$pageArray[2], 0, 30);  // retourne "abcde"
            $date = gmdate("Y-m-d")."T".gmdate("H:i:s")."Z";
            $page='<mediawiki xmlns="http://www.mediawiki.org/xml/export-0.10/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"xsi:schemaLocation="http://www.mediawiki.org/xml/export-0.10/ http://www.mediawiki.org/xml/export-0.10.xsd" version="0.10" xml:lang="fr">
                    
                    <page>
                        <title>'.$pageArray[0].'</title>
                        <ns>0</ns>
                        <id>ID de la page</id>
                        <revision>
                          <id>ID de la révision ?.</id>
                          <timestamp>'.$date.'</timestamp>'.'
                          <contributor>
                            <username>'.'importing-Tool'.'</username>
                            <id>1</id>
                          </contributor>
                          <comment>'.$preview.'</comment>
                          <model>wikitext</model>
                          <format>text/x-wiki</format>
                          <text xml:space="preserve" bytes="12">'.$pageArray[1].'</text>
                          <text xml:space="preserve" bytes="12">'.$pageArray[2].'</text>
                          <sha1>753hugogitqwnlby9d3k5rdzsa58oj1</sha1>
                        </revision>
                      </page>
                    </mediawiki>';

                    $my_file = 'PageStorage/'.$pathToFolder.'/'.$pageArray[0].'.xml';
                    $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
                    fwrite($handle, $page);
                    fclose($handle);
                    
                    return $my_file;
        }

        function zipFile($nomDossier) {
            /*
             * récupère la liste des fichiers d'un dossier puis les mets dans une liste.
             */
            $path = 'PageStorage/'.$nomDossier.'/';
            $fileList = glob($path.'*');
            $listeFichiers = array();
            $size = strlen('PageStorage/') + strlen($nomDossier)+1;

            foreach($fileList as $filename){
               $filename = substr($filename, $size);
               array_push($listeFichiers, $filename);
            }
            /*
             *Creation du zip avec les fichiers a l'interieur
             */
            $zip = new ZipArchive;
            $tmp_file = 'PageStorage/'.$nomDossier.'.zip';
            if ($zip->open($tmp_file,  ZipArchive::CREATE)) {
                foreach($listeFichiers as $fichier){
                    $zip->addFile($path.$fichier, $fichier);
                }
                $zip->close();
            } else {
                echo 'Failed!';
            }
        }

        /*
         * Download a zip file using php headers
         */
        function dlPage($filePath){
          header('Content-Type: application/octet-stream');
          header("Content-Transfer-Encoding: Binary"); 
          header("Content-disposition: attachment; filename=\"" . basename($filePath) . "\""); 
          readfile($filePath);
        }


     }
?>