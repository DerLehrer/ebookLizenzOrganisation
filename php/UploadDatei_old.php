<?php
/* Session verfügbar machen */
require("db_zugriff.php");


/*Seite nur für Verwalter verfügbar machen */
?>
<?php if($_SESSION["benutzer"]=="Verwalter" ) : ?>

<!DOCTYPE html>
<html>
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1"> <!--Bootstrap für alle Groessen-->

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/select.dataTables.min.css">
        <link rel="stylesheet" href="css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="css/meineFormatierungen.css">
        
        <style>
        input[type="file"] {
            position: fixed;
            right: 100%;
            bottom: 100%;
        }
                  </style>

    </head>
    <body>
        <div class="container-fluid ">
        
        <form  id="formular"  "enctype="multipart/form-data" >
                    <label for="inputFile">
                    <div class="btn btn-outline-primary" id="ohne" value="Neue B&uumlcher hinzuf&uumlgen" style="width: 200px">Neue B&uumlcher hinzuf&uumlgen</div>
            </label>
            <input type="file" id="inputFile">
         </form>
         <p>
        <button class="btn btn-outline-primary" id="starten" value="losgehts" style="width: 200px">GO</button>
    </p>
     <!--  <progress value="0" max="100"></progress>-->

   <p>Vorhandene Buecher</p>
<p>
    <div id="output">
    </div>  
    
</p>
</div>


<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="js/jquery.dataTables.min.js" defer></script>
        <script type="text/javascript" src="js/dataTables.buttons.min.js" defer></script>
        <script type="text/javascript" src="js/dataTables.select.min.js" defer></script>
        <script type="text/javascript" src="js/controllerUpload.js"></script>





    </body>
    <?php endif; ?>