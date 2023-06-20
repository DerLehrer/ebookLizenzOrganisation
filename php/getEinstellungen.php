<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){
setlocale(LC_ALL,"de_DE.UTF8");

/*if($_POST["aufruf"] == "fehlendeCodes"){

$query = "SELECT bestellung.BestellerID, bestellung.BuchID, bestellung.Datum, codes.Code as Code
          FROM bestellung LEFT OUTER JOIN codes ON (bestellung.BuchID = codes.buchID AND BestellerID = codes.Zugeordnet)";

}
*/

$query="SELECT Schulname, Direktor, Strasse, PLZ, Ort, Admin, Email, Einladung FROM schuldaten";

$abfrage = $Datenbank->query($query);

$ausgabearray = array();
	while($zeile = $abfrage->fetch_object()) {
        $ausgabearray[]= $zeile;
	}

echo json_encode($ausgabearray);
}}
?>