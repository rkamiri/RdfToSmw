<?php    
    class View {
        public function getBody(){
           echo'<!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>rdfToSmw</title>
                    <link rel="stylesheet" href="style/index-style.css">
                </head>
                <header>
                </header>
                <body>
                    <form enctype="multipart/form-data" action="index.php?module=index&action=upload" method="POST">
                        <input type="file" name="uploaded_file"></input><br />
                        <input type="submit" value="Upload"></input>
                    </form>
                </body>
                <footer id="contact">
                    
                </footer>
                </html>';
        }
    }
?>