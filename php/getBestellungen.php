<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){
setlocale(LC_ALL,"de_DE.UTF8");

$query = "SELECT BestellerID, BuchID, Datum, Code FROM bestellung";

	$abfrageergebnis = $Datenbank->query($query);
	$arr = array();
	while($r = mysqli_fetch_object($abfrageergebnis)) {
    	$arr[]= $r;
	}
	echo json_encode($arr);
}}
?>