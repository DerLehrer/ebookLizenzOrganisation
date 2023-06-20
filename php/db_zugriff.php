    <?php

	$consts = parse_ini_file("infodat.ini");

	$hostname_Datenbank = $consts['dbhost'];
    $database_Datenbank = $consts['db'];
	$username_Datenbank = $consts['dbusername'];
	$password_Datenbank = $consts['dbpassword'];
	$port = $consts['dbport'];
	$Datenbank = mysqli_connect($hostname_Datenbank, $username_Datenbank, $password_Datenbank,$database_Datenbank,$port);
	$Datenbank->set_charset("utf8mb4");

?>