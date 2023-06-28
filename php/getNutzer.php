<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){

setlocale(LC_ALL,"de_DE.UTF8");

$queryzwei = "SELECT benutzer.Email, benutzer.SchuelerVname, benutzer.SchuelerNname, benutzer.Klasse, benutzer.Gesetzt, benutzer.Eingeladen, suba.Kosten FROM benutzer 
LEFT OUTER JOIN (SELECT sum(buch.Preis) AS Kosten, bestellung.bestellerID AS nutzer FROM bestellung, buch WHERE buchID = buch.Buch GROUP BY bestellerID) AS suba
ON benutzer.Email = suba.nutzer WHERE benutzer.Name NOT LIKE 'Verwalter' ;";

	$abfrageergebnis = mysqli_query($Datenbank,$queryzwei);
	$arr = array();
	while($r = mysqli_fetch_object($abfrageergebnis)) {
    	$arr[]= $r;
	}
	echo json_encode($arr);

}}

?>