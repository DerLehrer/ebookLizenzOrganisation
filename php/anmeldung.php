<?php
require_once("session.php"); 
require_once("db_zugriff.php"); 
  setlocale(LC_ALL,"de_DE.UTF8");

     if(isset($_POST['lN'])){
 

 $lN = $_POST['lN'];
 $PW = $_POST['pW'];
    

    $stmt = $Datenbank->prepare("SELECT Hashcode, Name, Email FROM benutzer WHERE Name like ? OR Email like ? ;");
    $stmt->bind_param("ss", $lN, $lN);
    $stmt->execute();
    $checkUserName = $stmt->get_result();

    if($checkUserName->num_rows == 1){
        $datensatz = $checkUserName->fetch_assoc();
       $_SESSION["benutzer"]=$datensatz["Name"];
       $_SESSION["benutzerID"]=$datensatz["Email"];
        $hash = $datensatz["Hashcode"]; 
        $wert = password_verify($PW, $hash);
        $stmt->close();

echo json_encode($wert);

   }
    }
?>