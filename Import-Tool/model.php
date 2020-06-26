<?php
     class Model{
        function __construct(){
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
            $page='<mediawiki xmlns="http://www.mediawiki.org/xml/export-0.10/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.mediawiki.org/xml/export-0.10/ http://www.mediawiki.org/xml/export-0.10.xsd" version="0.10" xml:lang="fr">
                   <page>
                      <title>'.$pageArray[0].'</title>
                        <ns>0</ns>
                        <id>0</id>
                        <revision>
                          <id>0</id>
                          <timestamp>'.$date.'</timestamp>'.'
                          <contributor>
                            <username>'.'Romain'.'</username>
                            <id>1</id>
                          </contributor>
                          <comment>'.$preview.'</comment>
                          <model>wikitext</model>
                          <format>text/x-wiki</format>';
                          $page=$page.'<text xml:space="preserve" bytes="12">';

                          if($pageArray[1]!="" || isset($pageArray[1]))
                           $page=$page.$pageArray[1];
                          if($_SESSION['viki']=='true'){
                           $page=$page.'{{ #viki:pageTitles='.$pageArray[0].'}}';
                          }
                          if(($pageArray[2]!="" || isset($pageArray[2])) && $_SESSION['viki']==='true'){
                            $pieces = explode(' ', $pageArray[2]);
                            $last_word = array_pop($pieces);
                            $page=$page.'['.$_SESSION['url'].$last_word.' '.$pageArray[2].']';
                          }
                          else{
                            $page=$page.$pageArray[2];
                          }
                          $page=$page.'</text>
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

        /*
         * Add the file contained in a folder to a zip file
         */
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
        function dlZip($filePath){
          header('Content-Type: application/octet-stream');
          header("Content-Transfer-Encoding: Binary"); 
          header("Content-disposition: attachment; filename=\"" . basename($filePath) . "\""); 
          readfile($filePath);
        }

        public static function removeFolder($dirPath) {
          if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
          }
          if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
          }
          $files = glob($dirPath . '*', GLOB_MARK);
          foreach ($files as $file) {
            if (is_dir($file)) {
              self::deleteDir($file);
            } else {
              unlink($file);
            }
          }
          rmdir($dirPath);
        }
     }
?>