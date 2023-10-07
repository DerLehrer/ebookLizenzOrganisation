<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){
setlocale(LC_ALL,"de_DE.UTF8");


$queryeins = "SELECT suba.BuchT, suba.Codes,  subb.BestellerId, IF(subb.BestellerId IS NULL,0, 1) AS Anzahl_verwendungen, suba.Anzahl_max FROM (SELECT buch.Buch AS BuchT, Codes, Anzahl_max FROM buch, codes WHERE buch.Buch = codes.BuchId  AND Anzahl_max < 2) as suba left outer join (SELECT Code, BuchId, BestellerId FROM bestellung) as subb on suba.Codes = subb.Code And subb.BuchId = suba.BuchT ";
$queryzwei = "SELECT subc.BuchT, subc.Codes,  subd.BestellerId, IF(subd.BestellerId IS NULL,0,subd.Verwendungen)  AS Anzahl_verwendungen, subc.Anzahl_max FROM  (SELECT buch.Buch AS BuchT, Codes, Anzahl_max FROM buch,  codes WHERE buch.Buch = codes.BuchId and Anzahl_max >1)  as subc left outer join (SELECT COUNT(*) AS Verwendungen, BuchId, 'geteilt' BestellerId, Code FROM bestellung GROUP BY Code, BuchId) AS subd on subc.Codes = subd.Code And subd.BuchId = subc.BuchT";
$query = $queryeins . " UNION " . $queryzwei;

	$abfrageergebnis = mysqli_query($Datenbank,$query);		
	
	$arr = array();
	while($r = mysqli_fetch_object($abfrageergebnis)) {
    	$arr[]= $r;
	}
	echo json_encode($arr);
}}
?>