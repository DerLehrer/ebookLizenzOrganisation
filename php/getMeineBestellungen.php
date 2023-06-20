<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
setlocale(LC_ALL,"de_DE.UTF8");

$abfrager = $_SESSION['benutzerID'];

$query = "SELECT IF(suba.bestellt>0, '&nbsp;', '') as Bestellt , buch.Buch, buch.Stufe, buch.Titel, buch.Autoren, buch.Preis, suba.Code
				FROM buch left outer join (SELECT COUNT(Datum) AS bestellt, buchID, Code FROM bestellung WHERE BestellerID = '$abfrager' GROUP BY buchID) as suba
				on buch.Buch = suba.buchID;";
			
	$abfrageergebnis = mysqli_query($Datenbank,$query);
	$arr = array();
	while($r = mysqli_fetch_object($abfrageergebnis)) {
    	$arr[]= $r;
	}

	echo json_encode($arr);

}
?>