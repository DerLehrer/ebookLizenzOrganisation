<?php
require_once("session.php");
$const = parse_ini_file("infodat.ini");
$farbe = "<nav class='navbar navbar-expand-lg bg-primary navbar-dark'>";
$link ="<a href='../Anmeldung.html' >Neu anmelden";
if(isset($_SESSION["benutzer"]) && $_SESSION["benutzer"]=="Verwalter"){
    $farbe="<nav class='navbar navbar-expand-lg bg-success navbar-dark'>";
    $link="<a href='../Anmeldung.html' style='color:#28a745'>Neu anmelden";
};
unset($_SESSION["benutzer"]);
?>
<?php if(!isset($_SESSION["benutzer"])) : ?>
   <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--Bootstrap für alle Groessen-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/jquery-ui.css">
        <link rel="stylesheet" href="../css/jquery-ui.theme.css">
        <link rel="stylesheet" href="../css/jquery-ui.structure.css">
        <link rel="stylesheet" href="../css/meineFormatierungenBlau.css">
    </head>
    <body>
        <?php echo($farbe); ?>
            <a class="navbar-brand" href="abmelden.php" id="titel">&nbsp;</a>
        </nav>
        <br>
        <div class="container-fluid ">
            <div class="horizontalzentriert">
                <table class="zentriertesTab" id="inhalte">
                    <tr>
                        <td class="zentriertesTab">
                            <h4>Sie haben sich erfolgreich abgemeldet.</h4>
                        </td>
                    </tr>
                    <tr>
                    <td class="zentriertesTab">
                            <h4><?php echo($link); ?></a></h4>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>