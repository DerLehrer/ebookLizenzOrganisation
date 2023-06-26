<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");
?>
<?php if (!isset($_SESSION["benutzer"]) || time() > $_SESSION["ablaufzeit"] ) : ?>
    <head>
        <meta http-equiv='refresh' content='0; URL=../Anmeldung.html'>
    </head>
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
        <link rel="stylesheet" href="../css/meineFormatierungenBlau.css">
    </head>

    <body>
    
        <nav class="navbar navbar-expand-lg bg-primary navbar-dark">
            <a class="navbar-brand" href="abmelden.php" id="titel"><?php echo($_SESSION["benutzer"]); ?> abmelden</a>
            <ul class="nav navbar-nav ml-auto w-100 justify-content-end">
            <li class="nav-item">
                <a class="nav-link" href="javascript:schreibMail()"><div id="ansprechpartner"></div></a>
            </li>
            </ul>
        </nav>
        <br>
        <div class="container-fluid">
            <div class="horizontalzentriert">
                <table class="zentriertesTab" id="inhalte">
                    <tr>
                        <td class="zentriertesTab">
                            <h4>Ihre Bestellungen</h4>
                        </td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">
                            <div id="hinweise" style="font-size:small">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div id="buttonleiste" style="float: left; width: 100%;"></div>
                            <div style="float: none ;"></div>
                            <div id="buttonleiste2" style="float: right; margin-right: 0px;"></div>
                    <tr>
                        <td class="zentriertesTab">
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
                    <tr></tr>
                </table>
            </div>
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

        <script type="text/javascript" src="../js/controllerBestellungen.js"></script>

        <!-- Modale Dialoge -->
        <div style="display:none;">
            <div id="dialog-kaufen" title="Ausgew&auml;hlte E-Book-Codes bestellen?">
                <table>
                    <th></th>
                    <th></th>
                    <tr>
                        <td style="vertical-align: bottom;">
                         <div style="background-color: white; border:0px"><i class="fa-solid fa-triangle-exclamation fa-2xl" style="float:left; color:orange; margin:12px 12px 20px 0;"></i></div>
                        </td>
                        <td>Wollen Sie die ausgew&auml;hlten E-Book-Codes wirklich bestellen?<br>
                           <b> Hierf&uuml;r fallen Kosten in H&ouml;he von <div id="Kosten"  style="display: inline;">&nbsp;</div> an!</b>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="display:none;">
            <div id="dialog-hochladen" title="Eintr&auml;ge hinzuf&uuml;gen">
                <div id="dateiInfo" style="text-align: center">&nbsp;</div>
                <form id="formular" "enctype=" multipart/form-data">
                    <label for="inputFile" style="width: 100%">
                        <div class="btn btn-outline-success" id="ohne" value="Datei ausw&auml;hlenn" style="width: 100%">Datei ausw&auml;hlen</div>
                    </label>
                    <div style="display:none;"><input type="file" multiple="multiple" id="inputFile" /></div>
            </div>
        </div>

        <div id="nutzerid" style="display:none;"><?php echo($_SESSION['benutzerID']); ?></div>
        <div id="seitenid" style="display:none;">benutzerseite</div>

        <div id="gesetzliches" class="gesetzliches g-blau">
        <ul>| 
                    <a id="Impressum" href="#">Impressum</a> | 
                    <a href="https://gmg.amberg.de"><img src="../css/images/GMGb.png" style="height: 1em;"></a> | 
                    <a id="Datenschutz" href="#">Datenschutz</a> |
                </ul>
            </div>

    </body>

    </html>

<?php endif; ?>