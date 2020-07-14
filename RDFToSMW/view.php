<?php    
    class View {
        public function getBody(){
           echo'<!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>rdfToSmw</title>
                    <link rel="stylesheet" href="style/style.css">
                </head>
                <header>
                    <script type="text/javascript">
                            function addToForm() {
                                var queryForm = document.getElementById("form");
                                let modificator = document.getElementById("url");
                                if(document.getElementById("viki").checked ==true){
                                   modificator.style.display="block";
                                   modificator.required=true;
                                }
                                else{
                                    modificator.style.display="none";
                                    modificator.required=false;
                                } 
                            }
                    </script>
                </header>
                <body>
                    <div id="formContainer">
                        <form id="form" enctype="multipart/form-data" action="index.php?module=index&action=upload" method="POST"> 
                            
                            <input id="file" type="file" accept=".xml, .rdf, .owl" name="uploaded_file" required hide> </input>                          
                            
                            <label class="switch">
                                <input class="inputFile" type="checkbox" id="viki" name="viki" value="true" onclick="addToForm()">
                                <span class="slider round"></span>
                            </label>

                            <label for="viki" id="vikiLab"> Utiliser Viki ?</label>

                            <input type="text" id="url" name="url" placeholder="url to the homepage of mediawiki">                      
                            
                            <input id="btn" type="submit" value="Upload" ></input>
                        </form>
                    </div>
                </body>
                <footer id="contact">
                    
                </footer>
                </html>';
        }
    }
?>