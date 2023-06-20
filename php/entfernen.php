<?php
header('Content-Type: application/json; charset=utf-8');
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){
 
setlocale(LC_ALL,"de_DE.UTF8");

$auswahl = json_decode(($_POST['auswahl']));

$tabelle = $auswahl[0];

if($tabelle=='buecher'){$tabellenname= 'buch';}
else if($tabelle=='nutzer'){$tabellenname='benutzer';}
else if($tabelle=='codes'){$tabellenname='codes';}
else if($tabelle=='bestellungen'){$tabellenname='bestellung';}

$abfrage = $Datenbank->query("SELECT count(*) as Anzahl FROM ".$tabellenname.";");
$AnzahlEintraege = $abfrage->fetch_object()->Anzahl;

// Loeschen mit zweiteiligem Primaerschluessel



/* if($tabellenname=='codes'){
    
for($i=1; $i<sizeof($auswahl); $i=$i+2){                                // Erster Wert des Arrays = tabelle -> Start mit 1 //
    
    //Erst BuchID ermitteln (obsolet - aendern!)
    $abfr = $Datenbank->query( "SELECT Buch from buch WHERE Buch LIKE '".htmlspecialchars_decode($auswahl[$i])."';");   
    $BuchId = $abfr->fetch_object()->Buch;
    $Datenbank->query("DELETE FROM ".$tabellenname." WHERE BuchId LIKE '".$BuchId."' AND Codes LIKE '".htmlspecialchars_decode($auswahl[$i+1])."';");  
}
}  */

if($tabellenname=='bestellung' || $tabellenname =='codes'){
    
    $abfrage = $Datenbank->query("SELECT Column_Name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tabellenname."' limit 2;");
	while($r = mysqli_fetch_object($abfrage)) {
    	$primschl[]= $r->Column_Name;
	}
    for($i=1; $i<sizeof($auswahl); $i=$i+2){                               // Erster Wert des Arrays = tabelle -> Start mit 1 //
        ($Datenbank->query("DELETE FROM ".$tabellenname." WHERE ".$primschl[0]." LIKE '".htmlspecialchars_decode($auswahl[$i])."' AND ".$primschl[1]." LIKE '".htmlspecialchars_decode($auswahl[$i+1])."';"));
    }
    }
  

// einfaches Loeschen 
else{
// Ermittle erste Spalte (Primärschlüssel) ///////////////////////////////////////////////////////////
$abfrage = $Datenbank->query("SELECT Column_Name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tabellenname."' limit 1;");
$primschl = $abfrage->fetch_object()->Column_Name;

for($i=1; $i<sizeof($auswahl); $i++){                               // Erster Wert des Arrays = tabelle -> Start mit 1 //
    ($Datenbank->query("DELETE FROM ".$tabellenname." WHERE ".$primschl." LIKE '".htmlspecialchars_decode($auswahl[$i])."';"));
}
}

$abfrage = $Datenbank->query("SELECT count(*) as Anzahl FROM ".$tabellenname.";");
$neueAnzahl = $abfrage->fetch_object()->Anzahl;

$differenz = ($AnzahlEintraege-$neueAnzahl);

echo json_encode($differenz);
}}
?>