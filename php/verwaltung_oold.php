<?php
require("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");
?>

<?php if (true) : ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--Bootstrap für alle Groessen-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/select.dataTables.min.css">
        <link rel="stylesheet" href="../css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="../css/meineFormatierungen.css">
    </head>

    <body>
        <nav class="navbar navbar-expand-sm bg-success navbar-dark" >
            <a class="navbar-brand" href="#">VERWALTUNG</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item nav-item-aktiviert">
                        <a class="nav-link navlink-aktiviert" href="#">B&uuml;cher und Codes</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="#">Nutzer und Bestellungen </a>
                    </li>
                    <li class="nav-item " id="NavEinstell">
                        <a class="nav-link" href="#">Einstellungen </a>
                    </li>
                </ul>
            </div>
        </nav>
        <br>

        <div class="container-fluid " >
            <div class="horizontalzentriert">
                <table class="zentriertesTab" id="inhalte">

                    <tr>
                        <td class="zentriertesTab">
                            <h4>Vorhandene Bücher:</h4>
                        </td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">
                            <div id="hinweise" style="font-size:small">&nbsp;</div>
                        </td>
                    </tr>
                    <tr><td>
                            <div id="buttonleiste" style="float: left; width: 400px;"></div>
                            <div style="float: none ;"></div>
                            <div id="buttonleiste2"  style="float: right; margin-right: 0px;"></div>
                     <tr>
                        <td class="zentriertesTab">
                             <br>
                             <div id="output">&nbsp;</div>
                        </td>
                    </tr>    
                        </td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">
                            <div id="platzhalter" style="font-size:small">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                       
                    </tr>


                </table>
            </div>



            <script type="text/javascript" src="../js/jquery-3.6.1.min.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Alles in einem Script      -->  
        <script type="text/javascript" src="../datatables/datatables.js" defer></script>
    

            <script type="text/javascript" src="../js/jquery.dataTables.min.js" defer></script>
            <script type="text/javascript" src="../js/dataTables.buttons.min.js" defer></script>
            <script type="text/javascript" src="../js/dataTables.select.min.js" defer></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
            <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js" defer></script>
            <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js" defer></script>

            <script type="text/javascript" src="../js/controllerVerwaltung.js"></script>




    </body>

    </html>
<?php endif; ?>