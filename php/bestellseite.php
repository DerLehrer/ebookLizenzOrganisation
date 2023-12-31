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
        <link rel="stylesheet" href="../css/fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/fontawesome/css/solid.css" >
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">  
        <link rel="stylesheet" href="../css/meineFormatierungenBlau.css">
 
    </head>

    <body>
    

        <nav class="navbar bg-primary navbar-dark">
        <div class="navbar-header">
            <a class="navbar-brand" href="abmelden.php" id="titel"><?php echo($_SESSION["benutzer"]); ?> abmelden</a>
        </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item"> <a class="nav-link" href="javascript:schreibMail()"><div id="ansprechpartner"></div></a></li>
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
                            <div style="float: left; ">
                                     <div id="buttonleiste" ></div>
                                     <div id="buttonleiste3 "><input type="search" id="suche" class="form-control suche" placeholder="Suche"></div>
                            </div>
                            <div class="conditionalAlignment">
                                       <div id="buttonleiste2" ></div> 
                                       <div id="buttonleiste4"></div>
                            </div>     
                        </td>
                     </tr>
                </table>
            </div>
        </div>
        
       

        <div id="output"><table id="seitentabelle" class="table"  style="width:100%;"><thead style="background-color: white"><tr><th></th><th>Auswahl</th><th data-priority="11">Buch</th><th  data-priority="1">Jgst.</th><th data-priority="1">Fach</th><th>Verlag</th><th data-priority="2">Preis</th><th data-priority="2">Ihr Code</th></tr></thead></table></div>
                        <table>      
                    <tr>
                        <td class="zentriertesTab">
                            <div id="platzhalter" style="font-size:small">&nbsp;</div>
                        </td>
                    </tr>
                    <tr></tr>
                </table>
    
        <script type="text/javascript" src="../js/jquery-3.6.1.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="../datatables/datatables.js" defer></script>
        <script type="text/javascript" src="../js/jquery.dataTables.min.js" defer></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js" defer></script>
    
        <script type="text/javascript" src="../js/dataTables.buttons.min.js" defer></script>
        <script type="text/javascript" src="../js/dataTables.select.min.js" defer></script>
        <script type="text/javascript" src="../js/moment.min.js" defer></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js" defer></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js" defer></script>
        <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js" defer></script>
        <script src="../js/jquery-ui.js"></script>

        <script type="text/javascript" src="../js/controllerBestellungen.js?v2"></script>  <!--?v2 = Versionsnummer -->

        <!-- Modale Dialoge -->
        <div style="display:none;">
            <div id="dialog-kaufen" title="Ausgew&auml;hlte E-Book-Codes bestellen?" style="max-width:600px" >
                <table>
                    <th></th>
                    <th></th>
                    <tr>
                        <td style="vertical-align: center;">
                         <div style="background-color: white; border:0px"><i class="fa-solid fa-triangle-exclamation fa-2xl" style="float:left; color:rgb(220 53 69 / 80%); margin:12px 12px 20px 0;"></i></div>
                        </td>
                        <td>Wollen Sie die ausgew&auml;hlten E-Book-Codes wirklich bestellen?<br>
                           <b> Hierf&uuml;r fallen Kosten in H&ouml;he von <div id="Kosten"  style="display: inline;">&nbsp;</div> an!</b><br>
                           <table style="margin-top: 3px"><tr><td style="vertical-align:top"><input type=checkbox id="widerrufsrechtEinverstanden"/></td>
                           <td><div id="widerrufsverzichtserklaerung" style="font-size:0.8em">Hiermit erteile ich meine Zustimmung zur unverz&uumlglichen Ausf&uumlhrung des Vertrags und nehme zur Kenntnis, dass ich mein Recht auf Widerruf des Vertrags verliere, sobald der digitale Inhalt zur Verf&uumlgung gestellt wurde.</div></td>
                            </tr></table>
                        </td>
                    </tr>
                </table>
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