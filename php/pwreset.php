<?php
    require("db_zugriff.php");
    setlocale(LC_ALL, "de_DE.UTF8");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../phpmailer/src/Exception.php';
    require '../phpmailer/src/PHPMailer.php';
    require '../phpmailer/src/SMTP.php';

$constants = parse_ini_file("infodat.ini");

/* Einstellung für Dauer der Rücksetzbarkeit */
$Resetzeit = '00:15:00';
$Sperrzeit = '18:00:00';        //bei wiederholtem Scheitern

if($_POST){
$lN = $_POST["lN"];
trim($lN);

$abfrage = $Datenbank->query("SELECT Schulname, Admin, Email FROM schuldaten");
$datensatzObjekt = $abfrage->fetch_object();
$schulname = $datensatzObjekt->Schulname;
$schulmail = $datensatzObjekt->Email;
$versender = $datensatzObjekt->Admin;

/* Erstelle ein Einmalpasswort und ergänze dies beim Nutzer*/

    $stmt = $Datenbank->prepare("SELECT Email, Gesetzt, addTime(TIME(NOW())-Renew, '$Sperrzeit') FROM benutzer WHERE (Name LIKE ? OR Email LIKE ?) AND Gesetzt > 0");
    $stmt->bind_param("ss", $lN, $lN);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows == 1) {
        $datensatz = $res->fetch_array(); 
        $sendItTo = $datensatz[0];
        $spamschutz = $datensatz[1];
        $sperrstempel = $datensatz[2];
    }

    if($res->num_rows == 1 && ($spamschutz < 4 || $sperrstempel > 0 )){        //Entweder wenige gescheiterte Versuche oder Wartezeit erfolgt
    
    if($sperrstempel > 0 && $spamschutz > 3){$spamschutz = 0;}                 //Nach Wartezeit wird Gesetzt wieder auf 1 reduziert

    /*Einmalpasswort erstellen */
    $laenge = rand(20,24);
    $temporaeresPW ="";
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!*,;+-_()[]:";
    $var_size = strlen($chars);
    for( $x = 0; $x < $laenge; $x++ ) {  
    $temporaeresPW= $temporaeresPW.$chars[ rand( 0, $var_size - 1 ) ];
    }
           
    /* Achtung: Anführungszeichen bei Variablen, die als Text eingehen sollen sind wichtig! */
              
       $Datenbank->query("Update benutzer set RenewPW = '$temporaeresPW', Renew = addTime(Time(Now()), '$Resetzeit'), Gesetzt = $spamschutz+ 1 WHERE Email like '$sendItTo';");

        $betreff =  $schulname . ": Rücksetzung ihres Passworts";
        $betreff = "=?utf-8?b?" . base64_encode($betreff) . "?=";
       
        $text = "<html>Diese Mail wurde von der Anmeldeseite zur Code-Bestellung für ebooks verschickt.<br>
        Innerhalb der nächsten 15 Minuten können Sie Ihr Passwort neu vergeben.<br>
        Bitte verwenden Sie dafür folgenden Link:<br>
        <a href='
        https://ebooks.gmg-info.de/zuruecksetzen.php?name=$sendItTo&ePw=$temporaeresPW'>
        https://ebooks.gmg-info.de/zuruecksetzen.php?name=$sendItTo&ePw=$temporaeresPW
        </a><br>
        <br>
        Sollten Sie die Passwortzurücksetzung nicht angefordert haben, so können Sie diese Mail ignorieren. <br>
        Mit freundlichen Grüßen,<br>
        i.A.<br>
        $versender <br>
        $schulname</html>
        ";

$mail = new PHPMailer(TRUE);
 
$mail->setFrom($schulmail, $versender);
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
 

if(!$mail->Send()) {
  echo json_encode("-1");
} else {
 echo json_encode("1");
}
     
}
  else{echo json_encode("0");}
} 
?>
