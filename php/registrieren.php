 <?php
    require("db_zugriff.php");
    setlocale(LC_ALL, "de_DE.UTF8");

    if($_POST){
   $rN = trim($_POST["aN"]);     //AnmelderName
   $eM = trim($_POST["eM"]);     //Anmelder-Email

    $stmt = $Datenbank->prepare("SELECT Name FROM benutzer WHERE Name like ? OR Email like ?");
    $stmt->bind_param("ss", $rN, $rN);
    $stmt->execute();
    $checkUserName = $stmt->get_result();
       
    if ($checkUserName->num_rows == 0) {   
        $stmt->close();

        $options = [ 'cost' => 12 ];
        $hash = password_hash($_POST["pW"], PASSWORD_BCRYPT, $options);

        $stmt = $Datenbank->prepare("UPDATE benutzer SET Name =  ?, Hashcode = ?, Gesetzt = 1, Renew = 0, RenewPW = NULL WHERE Email like  ?");
        $stmt->bind_param("sss", $rN, $hash, $eM);
        $stmt->execute();
        $stmt->close();
   
        $stmt = $Datenbank->prepare("SELECT Name FROM benutzer WHERE Name like ? AND Hashcode like ? AND Email like ? AND Gesetzt = 1");
        $stmt->bind_param("sss", $rN, $hash, $eM);
        $stmt->execute();
        $checkUpdate = $stmt->get_result();

        if ($checkUpdate->num_rows == 1) {  /* Eintrag erfolgreich */
          echo (1);
            }
        else {
            echo (-1);                      /* Eintrag nicht erfolgreich wegen DB-Fehler*/
        }
    } else {
        echo (9999);                        /* Eintrag nicht erfolgreich, da Name schon vorhanden */
    }
    $stmt->close();
}
    ?>

    