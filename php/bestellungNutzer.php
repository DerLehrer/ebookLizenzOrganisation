<?php
header('Content-Type: application/json; charset=utf-8');
require_once("db_zugriff.php"); 
setlocale(LC_ALL,"de_DE.UTF8");

$auswahl = json_decode(($_POST['auswahl']));
$benutzer = $auswahl[0];

//Loesche bisherige Bestellungen
$stmt = $Datenbank->prepare("DELETE FROM bestellung WHERE BestellerID LIKE ? AND Code IS NULL");
$stmt->bind_param("s", $benutzer);
$stmt->execute();

//Fuege neue Bestellungen hinzu
$stmt = $Datenbank->prepare("INSERT IGNORE INTO bestellung(BestellerID, BuchID, Datum) VALUES (?,?, now())");
for($i=1; $i<sizeof($auswahl); $i++){
	$stmt->bind_param("ss",$benutzer, $auswahl[$i]);
	$stmt->execute();
}

$abfrage = $Datenbank->prepare("SELECT count(*) as Anzahl FROM bestellung WHERE BestellerID like ?;");
$abfrage->bind_param("s",$benutzer);
$abfrage->execute();
$ergebnis = $abfrage->get_result();
$anzahl = $ergebnis->fetch_object()->Anzahl;

$differenz = (sizeof($auswahl)-1-$anzahl);

echo json_encode($differenz);

?>

