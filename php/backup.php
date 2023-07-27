<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){

define('DS', DIRECTORY_SEPARATOR);

$backupname = date("Y_m_d");

$backupdir = '..'.DS.'backups';
$backup =  $backupdir.DS.$backupname.'.sql';

$mysqlDir = '..'.DS.'..'.DS.'..'.DS.'..'.DS.'usr'.DS.'bin';     // Paste your mysql directory here and be happy
$mysqldump = $mysqlDir.DS.'mysqldump'; 


exec("{$mysqldump} --user={$username_Datenbank} --password={$password_Datenbank} --host={$hostname_Datenbank} {$database_Datenbank} --result-file={$backup} 2>&1", $output, $result);

echo json_encode($backupdir);
}}
?>