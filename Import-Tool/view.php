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
                    <form action="index.php?module=index&action=createPage" method="POST">  
                        <input name="Title" placeholder="title" required>
                        <input name="UserName" placeholder="userName" required>
                        <input name="Preview" placeholder="Preview" required>
                        <textarea name="Body" placeholder="Body" required></textarea> 
                        <button type="submit">Envoyer</button>
                    </form>
                </body>
                <footer id="contact">
                    
                </footer>
                </html>';
        }
    }
?>