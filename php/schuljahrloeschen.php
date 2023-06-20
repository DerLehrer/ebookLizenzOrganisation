<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){

$Datenbank->query("DELETE FROM bestellung");
$Datenbank->query("DELETE FROM codes");
$Datenbank->query("DELETE FROM buch");
$Datenbank->query("DELETE FROM benutzer WHERE Name NOT LIKE 'Verwalter'");


echo json_encode(1);
}
}
?>

