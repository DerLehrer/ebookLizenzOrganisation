<?php
header('Content-Type: application/json; charset=utf-8');
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){

setlocale(LC_ALL,"de_DE.UTF8");

$mailUndCodeArray = json_decode($_POST['inhalt']);
 
$alleMailInhalteEinzeln = array();
$MailInhalteArray = array();

//Arrayinhalte in einzelne Mails aufteilen (dabei gleiche Adressaten zusammenfassen)
for ($i = 0; $i < sizeof($mailUndCodeArray)/3 ; $i = $i+3){
    $einInhalt = array();
    $einInhalt[] = $mailUndCodeArray[$i];
    $einInhalt[] = $mailUndCodeArray[$i+1];
    $einInhalt[] = $mailUndCodeArray[$i+2];
    $alleMailInhalteEinzeln[] = $einInhalt;
}

for($i = 0; $i < sizeof($alleMailInhalteEinzeln); $i++){
    if($alleMailInhalteEinzeln[$i] != null){
        //Codes für gleiche Adressaten zusammenfassen
        for($x = $i+1; $x < sizeof($alleMailInhalteEinzeln); $x++){
            if($alleMailInhalteEinzeln[$x] != null){
                if($alleMailInhalteEinzeln[$x][0] == $alleMailInhalteEinzeln[$i][0]){
                    $alleMailInhalteEinzeln[$i][] = $alleMailInhalteEinzeln[$x][1];
                    $alleMailInhalteEinzeln[$i][] = $alleMailInhalteEinzeln[$x][2];
                    unset($alleMailInhalteEinzeln[$x]);
                }
            }
    }
    $MailInhalteArray[] = $alleMailInhalteEinzeln[$i];          //Enthält ein Array für jede Email-Adresse: Email, Titel1, Code1, Titel2, Code2, ...
}

$abfrage = $Datenbank->query("SELECT schulname, admin, Email FROM schuldaten");
$datensatzObjekt = $abfrage->fetch_object();
$schulname = $datensatzObjekt->schulname;
$schulmail = $datensatzObjekt->Email;
$versender = $datensatzObjekt->admin;


for ($i = 0; $i < sizeof($MailInhalteArray);$i++) {
        $sendItTo = $MailInhalteArray[$i][0];
        $betreff = $schulname . ": Registrierung zur Code-Bestellung für ebooks";
        $betreff = "=?utf-8?b?" . base64_encode($betreff) . "?=";
        $from = "From: $versender traub@gmg.amberg.de\r\n";
        $from .= "Reply-To: $schulmail \r\n";
        $from .= "Content-Type: text/html\r\n";
        $text = "Sehr geehrte Erziehungsberechtigte,<br><br>hiermit erhalten Sie die Codes für die von Ihnen bestellen eBooks:<br><br>";

        for ($t = 1; $t < (sizeof($MailInhalteArray[$i]))/2; $t = $t+2){
                $text = $text.$MailInhalteArray[$i][$t].":   ".$MailInhalteArray[$i][1+$t]."<br>";
        }
        
        $text = $text."<br><br>
        Mit freundlichen Grüßen,<br>
        i.A.<br>
        $versender";

        mail($sendItTo, $betreff, $text, $from);
    }
}


}}
?>






