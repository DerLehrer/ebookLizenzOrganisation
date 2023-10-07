<?php
/* Sicherstellen, dass nur Eingeladene sich registrieren können */
require("php/db_zugriff.php");
    setlocale(LC_ALL, "de_DE.UTF8");

$anmelder = "";
$ePw = "";
if ($_GET) {
    $anmelder = $_GET["name"];
    $ePw = $_GET["ePw"];

} /* name wird nur genutzt, wenn auch etwas übergeben wurde */

$stmt = $Datenbank->prepare("SELECT Email FROM benutzer WHERE Email LIKE ? AND Gesetzt > 0 AND  timestampdiff(minute,  Renew, now())< 0;");

$stmt->bind_param("s", $anmelder);
$stmt->execute();
$checkEmail = $stmt->get_result();

if ($checkEmail->num_rows == 0) {
    echo ("<p><br>Eine Zurücksetzung ist nicht möglich oder die Zeit zur Zurücksetzung ist abgelaufen.</p>");
    exit();
}
$stmt->close();

?>
<?php if ($checkEmail->num_rows > 0) : ?>

    <!DOCTYPE html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--Bootstrap für alle Groessen-->

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/meineFormatierungen.css">
    </head>

    <body>
        <div class="container-fluid ">
            <div class="zentriert">
                <table class="zentriertesTab" id="inhalte">
                    <tr>
                        <td class="zentriertesTab">
                            <h3>Bitte setzen Sie Ihr Passwort neu:</h3>
                        </td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">
                            <div id="hinweiseBenutzername" style="font-size:small">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">
                            <form id="formular">
                               
                                <div id="hinweisePasswort" style="font-size:small">&nbsp;</div>
                                <input id="Passwort" class="btn btn-outline-info" type="password" value="Neues Passwort" onfocus="this.value=''" style="width: 200px ;text-align: center" onkeyup="checkPasswordStrength();">
                                <div id="togglePW" class="kleineSchrift"> <input type="checkbox">&nbsp;anzeigen</div>
                                <div id="pwOK" hidden>0</div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">
                            <div id="zuruecksetzen" class="btn btn-info" style="width: 200px ;text-align: center" onclick="setNeu()">Passwort speichern</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!--Übergabe der Email an JS -  optimierbar ?? -->
            <div id="anmelder" hidden>
                <?php echo ($anmelder); ?>
            </div>
            <div id="ePw" hidden>
                <?php echo ($ePw); ?>
            </div>



            <script type="text/javascript" src="js/jquery-3.6.1.min.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
            <script type="text/javascript" src="js/bCrypt.js"></script>
            <script type="text/javascript" src="js/controllerAnmeldung.js"></script>

            <div id="gesetzliches" class="gesetzliches g-info">
                <ul>| 
                    <a data-link="Impressum" href="#">Impressum</a> | 
                    <a data-link="Datenschutz" href="#">Datenschutz</a> |
                </ul>
            </div>

            <div id="seitenid" style="display:none;">zuruecksetzen</div>
    </body>
    </hmtl>
<?php endif; ?>
