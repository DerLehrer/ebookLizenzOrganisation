<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");
?>
<?php if (!isset($_SESSION["benutzer"]) || time() > $_SESSION["ablaufzeit"] ) : ?>
 <!-- #endregion -->
<head><meta http-equiv='refresh' content='0; URL=../Anmeldung.html'></head>
    
<?php elseif(isset($_SESSION["benutzer"])) : ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--Bootstrap für alle Groessen-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/select.dataTables.min.css">
        <link rel="stylesheet" href="../css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="../css/jquery-ui.css">
        <link rel="stylesheet" href="../css/jquery-ui.theme.css">
        <link rel="stylesheet" href="../css/jquery-ui.structure.css">
        <link rel="stylesheet" href="../css/fontawesome/css/all.min.css" />
        <link rel="stylesheet" href="../css/fontawesome/css/solid.css" >
        <link rel="stylesheet" href="../css/meineFormatierungen.css">

    </head>

     <body>
        <nav class="navbar navbar-expand-lg bg-success navbar-dark" >
            <a class="navbar-brand" href="abmelden.php" ><?php echo($_SESSION["benutzer"]); ?> abmelden</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbarLg">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbarLg">
          <ul class="navbar-nav">
                   <li class="nav-item ">
                        <a class="nav-link" href="nutzer.php">Nutzer</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="buch.php">B&uuml;cher</a>
                    </li>
                    <li class="nav-item nav-item-aktiviert">
                        <a class="nav-link  navlink-aktiviert" href="bestellungen.php">Bestellungen</a>
                    </li>
                     <li class="nav-item" >
                        <a class="nav-link " href="codes.php">Codes</a>
                    </li>
                 
                    <li class="nav-item " >
                        <a class="nav-link" href="einstellungen.php">Einstellungen </a>
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
                            <h4>Vorhandene Bestellungen</h4>
                        </td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">
                            <div id="hinweise" style="font-size:small">&nbsp;</div>
                        </td>
                    </tr>
                    <tr><td>
                            <div id="buttonleiste" style="float: left; width: 100%;"></div>
                            <div style="float: none ;"></div>
                            <div id="buttonleiste2"  style="float: right; margin-right: 0px;"></div>
                    </td>
                     <tr>
                        <td class="zentriertesTab">
                            
                             <div id="output"><table id="seitentabelle" class="table "  style="width:100%"><thead><tr><th></th><th>Besteller</th><th>Buch</th><th>Datum</th><th>Code</th></tr></thead><tbody></tbody></table></div>
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
            <script type="text/javascript" src="../datatables/datatables.js" defer></script>
            <script type="text/javascript" src="../js/jquery.dataTables.min.js" defer></script>
            <script type="text/javascript" src="../js/dataTables.buttons.min.js" defer></script>
            <script type="text/javascript" src="../js/dataTables.select.min.js" defer></script>
            <script type="text/javascript" src="../js/moment.min.js" defer></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
            <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js" defer></script>
            <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js" defer></script>
            <script src="../js/jquery-ui.js"></script>

            <script type="text/javascript" src="../js/controllerVerwaltung.js"></script>


<!-- Modale Dialoge -->

  <div style="display:none;">
            <div id="dialog-zuordnen" title="Codes zuordnen">
                <table>
                    <th></th>
                    <th></th>
                    <tr>
                        <td style="vertical-align: bottom;">
                         <div style="background-color: white; border:0px"><i class="fa-solid fa-triangle-exclamation fa-2xl" style="float:left; color:orange; margin:12px 12px 20px 0;"></i></div>
                        </td>
                          <td><div id="verteiltext">Wollen Sie die noch nicht zugeordneten Codes auf die vorhandenen Bestellungen verteilen?<br>
                            Die Zuordnung kann nicht r&uuml;ckg&auml;ngig gemacht werden!</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>


<div style="display:none;">
<div id="dialog-loeschen" title="Eintr&auml;ge l&ouml;schen?">
<table><th></th><th></th><tr><td ><div class="ui-state-error" style="background-color: white; border:0px"><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span></div></td>
<td>Wollen Sie die ausgew&auml;hlten Eintr&auml;ge  wirklich l&ouml;schen?<br>
  Der Prozess kann nicht r&uuml;ckg&auml;ngig gemacht werden!</td></tr></table>
</div>
</div>
</div>
<div style="display:none;">
<div id="dialog-hochladen" title="Eintr&auml;ge hinzuf&uuml;gen">
    <div id="dateiInfo" style="text-align: center">&nbsp;</div>
 <form  id="formular"  "enctype="multipart/form-data" >
    <label for="inputFile" style="width: 100%"><div class="btn btn-outline-success" id="ohne" value="Datei ausw&auml;hlenn" style="width: 100%">Datei ausw&auml;hlen</div></label>
   <div style="display:none;"><input type="file" id="inputFile"/></div>

 </div>
</div>

<div id="gesetzliches" class="gesetzliches g-gruen">
<ul class="g-gruen">|  
                    <a id="Impressum" href="#">Impressum</a> | 
                    <a href="https://gmg.amberg.de"><img src="../css/images/GMG.png" style="height: 1em;"></a> | 
                    <a id="Datenschutz" href="#">Datenschutz</a> |
                </ul>
</div>

<div id="seitenid" style="display:none;">bestellungen</div>
    </body>

    </html>




<?php endif; ?>