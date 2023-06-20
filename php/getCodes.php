<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){
setlocale(LC_ALL,"de_DE.UTF8");


$queryeins = "SELECT buch.Buch AS BuchT, Codes, BestellerId, Anzahl_verwendungen, Anzahl_max FROM buch, codes left outer join bestellung on codes.Codes = bestellung.Code WHERE buch.Buch = codes.BuchID AND Anzahl_max < 2 ";
$queryzwei = "SELECT buch.Buch AS BuchT, Codes, 'geteilt' BestellerId, Anzahl_verwendungen, Anzahl_max FROM codes , buch WHERE Anzahl_max > 1 AND buch.Buch = codes.BuchID";
$query = $queryeins . " UNION " . $queryzwei;

	$abfrageergebnis = mysqli_query($Datenbank,$query);		
	
	$arr = array();
	while($r = mysqli_fetch_object($abfrageergebnis)) {
    	$arr[]= $r;
	}
	echo json_encode($arr);
}}
?>