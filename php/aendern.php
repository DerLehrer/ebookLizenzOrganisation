<?php
header('Content-Type: application/json; charset=utf-8');
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){

require_once("db_zugriff.php"); 
setlocale(LC_ALL,"de_DE.UTF8");

$auswahl = json_decode(($_POST['auswahl']));

$tabelle = $auswahl[0];

//Alle Eintragungen muessen gegen Code-Injection abgefangen werden => prepared Statements

//Bucheintraege aendern
if($tabelle=='buecher'){

$reslt = $Datenbank->prepare("SELECT COUNT(*) AS zhl FROM bestellung WHERE BuchId = ?");
$reslt->bind_param("s",$auswahl[2]);
$reslt->execute();
$reslt->bind_result($anz);
$anzl = 0;
while($reslt->fetch()){
    $anzl= $anz;
}
if($anzl > 0){
$werte = ["key" => "alreadybooked", "anzahl" => 1];
echo json_encode($werte);
exit;
}
else{
$abfrage = "UPDATE buch SET Buch = ?, Stufe =? ,  Fach = ?,  Verlag = ?, Preis = ?  WHERE Buch = ?";
$stmt = $Datenbank->prepare($abfrage);
$stmt->bind_param("sissds",$auswahl[2], $auswahl[3],$auswahl[4],$auswahl[5],$auswahl[6], $auswahl[1] );
$stmt->execute();
echo json_encode($auswahl[1]);
exit;
    }
}


//Nutzereintraege aendern

else if($tabelle=='nutzer'){
//Vorname und Nachname trennen
$auswahl[3] = trim($auswahl[3]);
$name = explode(" ", $auswahl[3]);
//falls ein Namensteil fehlt: leeren Text ergänzen
if(sizeof($name)==1){$name[1]=$name[0];$name[0]="";}
if(sizeof($name)>2){
$neu="";
for($a=1;$a<sizeof($name);$a++){
$neu = $neu.$name[$a]." ";
}
$name[1]=$neu;
}
if($auswahl[5]=="Ja"){$auswahl[5]=1;}else{$auswahl[5]=0;}
$abfrage = "UPDATE benutzer SET Email =? , SchuelerVname =? ,  SchuelerNname = ?,  Klasse = ?, Eingeladen= ?  WHERE Email = ?";
$stmt = $Datenbank->prepare($abfrage);
$stmt->bind_param("ssssis",$auswahl[2],$name[0],$name[1],$auswahl[4],$auswahl[5],$auswahl[1] );
$stmt->execute();
echo json_encode($auswahl[1]);
exit;
    }

//Codeeintraege aendern

else if($tabelle=='codes' && $auswahl[5] > 0){

$reslt = $Datenbank->prepare("SELECT COUNT(*) AS zhl FROM bestellung WHERE Code = ?");
$reslt->bind_param("s",$auswahl[3]);
$reslt->execute();
$reslt->bind_result($anz);
$anzl = 0;
while($reslt->fetch()){
    $anzl= $anz;
}
if($anzl > $auswahl[5]){
$werte = ["key" => "toolow", "anzahl" => $anzl];
echo json_encode($werte);
exit;

}
else{
$abfrage = "UPDATE codes SET Anzahl_max = ?  WHERE Codes = ? AND BuchId = ?";
$stmt = $Datenbank->prepare($abfrage);
$stmt->bind_param("iss",$auswahl[5],$auswahl[3],$auswahl[2]);
$stmt->execute();
echo json_encode(1);
exit;
	}
    }


//Einstellungsdaten aendern

else if($tabelle=='einstellungen'){
$abfrage = "UPDATE schuldaten SET Sperre = ?, Schulname = ?, Direktor = ?, Strasse = ?, PLZ = ?, Ort = ?, Admin = ?, Email = ?, Einladung = ? ";
$stmt = $Datenbank->prepare($abfrage);
$stmt->bind_param("ssssissss",$auswahl[1],$auswahl[2],$auswahl[3],$auswahl[4],$auswahl[5],$auswahl[6],$auswahl[7],$auswahl[8],$auswahl[9]);
$stmt->execute();
echo json_encode(1);
exit;
    }
}
}
 ?>