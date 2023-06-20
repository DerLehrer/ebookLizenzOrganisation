<?php
   session_start();
 
    if(!isset($_SESSION["ablaufzeit"])){
        $_SESSION["ablaufzeit"] = time()+900; 
      }
  
    if( time() > $_SESSION["ablaufzeit"] && isset($_SESSION["benutzerID"]) ){
        unset($_SESSION["benutzer"]);
        unset($_SESSION["benutzerID"]);
        unset($_SESSION["ablaufzeit"]);
    }
    else{
        $_SESSION["ablaufzeit"] = time()+900; 
    }
?>
