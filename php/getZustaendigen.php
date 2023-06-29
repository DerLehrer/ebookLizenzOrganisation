<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {

setlocale(LC_ALL,"de_DE.UTF8");

//ergaenze zustaendigen Ansprechpartner
$query = "SELECT Sperre, Admin, Email FROM schuldaten;";

$abfrageergebnis = mysqli_query($Datenbank,$query);		
	
$arr = array();
while($r = mysqli_fetch_object($abfrageergebnis)) {
    $arr[]= $r;
}
echo json_encode($arr);
}
?>