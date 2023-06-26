<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){
setlocale(LC_ALL,"de_DE.UTF8");

$queryzwei = "SELECT buch.Buch, buch.Stufe, buch.Fach, buch.Verlag, buch.Preis, suba.Bestellungen, subb.Codes 
				FROM buch left outer join (SELECT Count(BestellerId) AS Bestellungen, BuchId FROM bestellung GROUP BY BuchId) AS suba
				on buch.Buch = suba.BuchId
				left outer join  (SELECT Count(Codes) AS Codes, Anzahl_max, Anzahl_verwendungen, BuchId FROM codes GROUP BY BuchId) AS subb
				on buch.Buch = subb.BuchId";
			
		
	$abfrageergebnis = mysqli_query($Datenbank,$queryzwei);
	$arr = array();

	while($r = mysqli_fetch_object($abfrageergebnis)) {
    	$arr[]= $r;
	}
	echo json_encode($arr);
}}
?>