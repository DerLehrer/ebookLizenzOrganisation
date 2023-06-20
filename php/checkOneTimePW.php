 <?php

    require_once("db_zugriff.php");
    setlocale(LC_ALL, "de_DE.UTF8");

    if($_POST){

    $eMail = trim($_POST["aN"]);
    $oTPW = trim($_POST["oTPW"]);

    //echo($eMail."->".$oTPW."->".$_POST['pW']);

    $options = [ 'cost' => 12 ];
    $hash = password_hash($_POST["pW"], PASSWORD_BCRYPT, $options);

    $stmt = $Datenbank->prepare("SELECT Email, Renew-TIME(NOW()) FROM benutzer WHERE Email LIKE ? AND RenewPW LIKE ?");
    $stmt->bind_param("ss", $eMail, $oTPW);
    $stmt->execute();

    /*Beachte: $stmt->get_result()->fetch_assoc() kann nur einmal genutzt werden!*/
    $res = $stmt->get_result();
    if($res->num_rows == 1) {
        $datensatz = $res->fetch_array(); 
        $id = $datensatz[0];
        $checkRenew = $datensatz[1];
    
    if($id != null && $checkRenew>0){
        $stmt2 = $Datenbank->prepare("UPDATE benutzer SET Hashcode = ?, RenewPW = NULL, Gesetzt = 1, Renew = NULL WHERE Email LIKE ?");
        $stmt2->bind_param("ss", $hash, $eMail);
        $stmt2->execute();
        $stmt2->close();
    }
    }
        $stmt2 = $Datenbank->prepare("SELECT Email FROM benutzer WHERE Hashcode like ? AND Email like ? AND Gesetzt = 1 AND RenewPW IS NULL AND Renew IS NULL" );
        $stmt2->bind_param("ss", $hash, $eMail);
        $stmt2->execute();
        $checkUpdate = $stmt2->get_result();

        if($checkUpdate->num_rows == 1) {  /* Eintrag erfolgreich */
             echo(1);             /* durch Erneuerung */
            }
        else {
            if($checkUpdate<=0){
                echo (-1);                      /* Eintrag nicht erfolgreich, da zu spaet*/
            } 
            else {
                echo (-2);                    /* Eintrag nicht erfolgreich, da kein Abfrageergebnis */
            }
        }
    $stmt->close();
    $stmt2->close();

    }
    ?>
