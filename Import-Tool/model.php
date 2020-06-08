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


        /*
         * Create a new SMW page in XML using the information of the form 
         * The file is stored insite the folder created by the system
         */

        //TODO heure serveur
        //TODO Questionnement BD pour ID page
        //TODO check id révision ?
        function createPage($pathToFolder){

            $date = gmdate("Y-m-d")."T".gmdate("H:i:s")."Z";
            $page='<mediawiki xmlns="http://www.mediawiki.org/xml/export-0.10/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"xsi:schemaLocation="http://www.mediawiki.org/xml/export-0.10/ http://www.mediawiki.org/xml/export-0.10.xsd" version="0.10" xml:lang="fr">
                    
                    <page>
                        <title>'.$_POST['Title'].'</title>
                        <ns>0</ns>
                        <id>ID de la page</id>
                        <revision>
                          <id>ID de la révision ?.</id>
                          <timestamp>'.$date.'</timestamp>'.'
                          <contributor>
                            <username>'.$_POST['UserName'].'</username>
                            <id>1</id>
                          </contributor>
                          <comment>'.$_POST['Preview'].'</comment>
                          <model>wikitext</model>
                          <format>text/x-wiki</format>
                          <text xml:space="preserve" bytes="12">'.$_POST['Body'].'</text>
                          <sha1>753hugogitqwnlby9d3k5rdzsa58oj1</sha1>
                        </revision>
                      </page>
                    </mediawiki>';

                    $my_file = 'PageStorage/'.$pathToFolder.'/'.$_POST['Title'].'.xml';
                    $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
                    fwrite($handle, $page);
                    fclose($handle);
                    
                    return $my_file;
        }

        function dlPage($filePath){
            /*
             * Partie du téléchargement réel
             */
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($filePath) . "\""); 
            readfile($filePath);
        }


     }
?>