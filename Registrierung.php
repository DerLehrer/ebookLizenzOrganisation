<?php

/* Sicherstellen, dass nur Eingeladene sich registrieren können */

require("php/db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

$anmelder = "";
$ePw = "";
if(array_key_exists("ePw", $_GET) && array_key_exists("name", $_GET)){
    $anmelder = ($_GET["name"]);
    $ePw = $_GET["ePw"];

} /* name wird nur genutzt, wenn auch etwas übergeben wurde */

$stmt = $Datenbank->prepare("SELECT email FROM benutzer WHERE Email LIKE ? AND (Gesetzt != 1 OR Renew-TIME(NOW())> 0);");
$stmt->bind_param("s", $anmelder);
$stmt->execute();
$checkEmail = $stmt->get_result();

$stmt->close();

if ($checkEmail->num_rows == 0) {
    echo ("<p><br>Sie sind schon registriert oder Ihre Registrierung ist nicht möglich.</p>");
    exit();
}

?>
<?php if ($checkEmail->num_rows > 0) : ?>

    <!DOCTYPE html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--Bootstrap für alle Groessen-->

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/meineFormatierungen.css">
        <link rel="stylesheet" href="css/meineFormatierungen_navy.css">
    </head>

    <body>
        <div class="container-fluid ">
            <div class="zentriert">
                <table class="zentriertesTab" id="inhalte">
                <tr><td class="zentriertesTab"><div id="info" style="font-size:small">GMG ebook Lizenzplattform</div></td></tr>
                       <tr>
                        <td class="zentriertesTab">
                            <h3>Bitte registrieren Sie sich</h3>
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
                                <input id="loginName" class="btn btn-outline-info" type="text" value="Benutzername" onfocus="this.value=''" style="width: 200px ;text-align: center" onkeyup="checkBenutzername();"><br>
                                <div id="loginOK" hidden>0</div>
                                <div id="hinweisePasswort" style="font-size:small">&nbsp;</div>
                                <input id="Passwort" class="btn btn-outline-info" type="password" value="Passwort" onfocus="this.value=''" style="width: 200px ;text-align: center" onkeyup="checkPasswordStrength();">
                                <div id="togglePW" class="kleineSchrift"> <input type="checkbox">&nbsp;anzeigen</div>
                                <div id="pwOK" hidden>0</div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">&nbsp;</td>
                    </tr>
                    <tr>
                    <td class="zentriertesTab" ><table style="width:350px;  margin: 0 auto;"><tr><td>
                        <td style="vertical-align:top"><input   type="checkbox" class ="regcheckbox" id="annahme"></td>
                        <td style=" padding: 3px"><div class="zustimmung" id="zustimmungstext">Ich habe die <a href = "datenschutzerklaerung_i.html">Datenschutzerkl&auml;rung</a> gelesen, kenne meine Rechte und stimme der Verarbeitung meiner Daten zum Zweck der Abwicklung der gemeinsamen Bestellung von ebook-Lizenzen zu.</div></td>
</td></tr></table>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="zentriertesTab">
                            <div id="registrieren" class="btn btn-info" style="width: 200px ;text-align: center" onclick="hashIt()">registrieren</div>
                        </td>
                    </tr>
                </table>
                </div> </div>

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
                    <a id="Impressum" href="#">Impressum</a> | 
                    <a href="https://gmg.amberg.de"><img src="css/images/GMGi.png" style="height: 1em;"></a> | 
                    <a id="Datenschutz" href="#">Datenschutz</a> |
                </ul>
            </div>
            <div id="seitenid" style="display:none;">registrierung</div>

    </body>
<?php endif; ?>