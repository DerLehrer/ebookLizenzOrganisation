<?php
require_once("session.php");
require_once("db_zugriff.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){

$constants = parse_ini_file("infodat.ini");

$aufrufvariable = "einladung";

$abfrage = $Datenbank->query("SELECT schulname, Admin, Einladung FROM schuldaten");
$datensatzObjekt = $abfrage->fetch_object();
$schulname = $datensatzObjekt->schulname;
$versender = $datensatzObjekt->Admin;
$einladungstext = $datensatzObjekt->Einladung;

$versandadresse = $constants['mailversandadresse'];

$zaehler = 0;

if ($aufrufvariable == "einladung") {
    $abf = $Datenbank->query("SELECT Email, RenewPW, Eingeladen FROM benutzer WHERE Name NOT LIKE 'Verwalter' AND Gesetzt < 1");
    while ($zaehler <= 40 && $datensatz = $abf->fetch_assoc()) {
        if($datensatz['Eingeladen'] == 0){
        $sendItTo = $datensatz['Email'];
        $otpw = $datensatz['RenewPW'];
        $betreff = $schulname . ": Registrierung zur Code-Bestellung für ebooks";
        $betreff = "=?utf-8?b?" . base64_encode($betreff) . "?=";
       
        $text = "<html>".$einladungstext.
        "<a href='https://ebooks.gmg-info.de/Registrierung.php?name=$sendItTo'>
        https://ebooks.gmg-info.de/Registrierung.php?name=$sendItTo&ePw=$otpw
        </a><br>
        Mit freundlichen Grüßen,<br>
        i.A.<br>
        $versender</html>";

$mail = new PHPMailer(TRUE);
 
$mail->setFrom($versandadresse, $versender);
$mail->addAddress($sendItTo);
$mail->Subject = $betreff;
$mail->Body = $text;
$mail->CharSet = 'utf-8';  
$mail->IsHTML(true);
 
/* SMTP parameters. */
$mail->isSMTP();
$mail->Host = $constants['mailhost'];
$mail->SMTPAuth = $constants['auth'];
$mail->SMTPSecure = $constants['sec'];
$mail->Username = $constants['mailusername'];
$mail->Password = $constants['mailpassword'];
$mail->Port = $constants['mailport']; 
 
//Senden der E-Mail
if(!$mail->Send()) {
} else {
  $Datenbank->query("UPDATE benutzer SET Eingeladen = 1 WHERE Email LIKE '".$sendItTo."';");
  $zaehler++;
}
}
    }

}
echo ($zaehler);

}}
?>





