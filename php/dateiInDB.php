<?php
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){

setlocale(LC_ALL,"de_DE.UTF8");

$separator = ";";

if(isset($_POST["sid"])){

//SeitenID (sid) entscheidet über Zieltabelle ////////////////////////////////////////////////////////
$tabelle = ($_POST ["sid"]);
$bucharr = array();                          //fuer Pruefung, ob Buch vorhanden, wenn Codes eingefuegt werden sollen 
$leerbleibendeSpalten;                       //muessen hinten sein!

if($tabelle=='buecher'){$tabellenname = 'buch'; $leerbleibendeSpalten = 0;}             
else if($tabelle=='nutzer'){$tabellenname = 'benutzer';  $leerbleibendeSpalten = 4;}    
else if($tabelle=='codes'){
    $tabellenname = 'codes';  
    $leerbleibendeSpalten = 1;
    //nur Codes laden, wenn ein entsprechendes Buch vorhanden ist -> Buchliste ermitteln
    $quer ="SELECT Buch from buch";
    $qu = mysqli_query($Datenbank,$quer);
        while($qub = $qu->fetch_object()) {
            $bucharr[]= $qub->Buch;
        }
}


$abfrage = $Datenbank->query("SELECT count(*) as Anzahl FROM ".$tabellenname.";");
$AnzahlEintraege = $abfrage->fetch_object()->Anzahl;

// Ermittle Spaltennamen und -anzahl //////////////////////////////////////////////////////////
$abfrage = $Datenbank->query("SELECT Column_Name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tabellenname."';");
$spaltenNamen = array();
while ($obj = $abfrage -> fetch_object()) {
    $spaltenNamen[] = $obj->Column_Name;
}
$anzahlSpalten = sizeof($spaltenNamen)-$leerbleibendeSpalten;

//Dateien einlesen/////////////////////////////////////////////////////////////////////////////////////
$anzahl = count ($_FILES ["zeugs"]["tmp_name"]);

//Jede Datei////////////////////////////////////////////////////////////////////////////////////////
for ($i = 0; $i < $anzahl; $i++) {
    if (!mb_check_encoding(file_get_contents($_FILES["zeugs"]["tmp_name"][$i]), 'UTF-8')) {              /// UTF8 sicherstellen
        echo json_encode("_7777_");
        exit;
    }

$inhalt = file($_FILES['zeugs']['tmp_name'][$i]);


//Prüft, ob die Spaltenzahl korrekt ist /////////////////////////////////////////////////////////////////////
 $arrZeile = explode($separator, $inhalt[0]);
if(($tabellenname != "benutzer" && sizeof($arrZeile)!=$anzahlSpalten) || ($tabellenname == "benutzer" && sizeof($arrZeile)!=$anzahlSpalten-2)){
    echo json_encode("_8888_");
    exit;
}


//erzeuge SQL für das Einfügen
$intospaltentext = "";
$fragezeichen ="";


//$query="";

// berücksichtige Auto-Increment in Benutzer- und Buch-Tabelle (entfaellt)
$i = 0;
$durchlauf = $anzahlSpalten;
//if($tabellenname == "benutzer"){$i++; $durchlauf++;}         //Id-Spalte vorneweg

// nutze nur Prepared-Statements um Schadcode abzufangen

//Bereite erforderliche Anzahl Platzhalter für die Query vor

for($i; $i<$durchlauf; $i++){
    $intospaltentext= $intospaltentext.$spaltenNamen[$i];
    $fragezeichen=$fragezeichen."?";
    if($i != $durchlauf-1 ){ 
        $intospaltentext = $intospaltentext.", "; $fragezeichen = $fragezeichen.", ";
        }
}

//zeilenweise auslesen und einfügen //////////////////////////////////////////////////////////////////////////
for($i=1;$i < count($inhalt); $i++){                    // Beginne mit 1, da erste Zeile (0) = Überschriften
    $arrZeile = explode($separator, $inhalt[$i]);       // Zeileninhalte als Array verfuegbar machen

    //Codes-Primärschlüssel verwendet BuchID, nicht Buchname/Titel (entfaellt)

   /*  if($tabellenname == "codes"){
        $abfr = "SELECT Id from buch WHERE Buch LIKE ? LIMIT 1";    //Limit 1 um Laufzeitfehler zu vermeiden - es sollte trotzdem jeden Titel nur einmal geben!
        $statm = $Datenbank->prepare($abfr);
        $statm->bind_param("s",trim($arrZeile[0]));
        $statm->execute();
        if($res = $statm->get_result()->fetch_object()) {
              $neueID = $res->Id;
              $arrZeile[0] = $neueID;     
	}
     
   }
  */

//else
     if($tabellenname == "buch"){
      $query = "INSERT IGNORE INTO ".$tabellenname."($intospaltentext) VALUES ($fragezeichen);";
      $paramets = array();
      $position = 0;
      for($az=0;$az < $anzahlSpalten; $az++){
            $paramets[] = trim($arrZeile[$position]);
            $position++;
            }   
        $parametsString = implode(", ", $paramets);                //erstelle String mit Trennzeichen aus Array 


     $stmt = $Datenbank->prepare($query);
      $types = str_repeat('s', count($paramets));      
     $stmt->bind_param($types, ...$paramets);
     $stmt->execute();
    }

    if($tabellenname == "codes" && in_array(trim($arrZeile[0]), $bucharr)){       
    $query = "INSERT IGNORE INTO ".$tabellenname."($intospaltentext) VALUES ($fragezeichen);";
    $paramets = array();
    $position = 0;
    for($az=0;$az < $anzahlSpalten; $az++){
        $paramets[] = trim($arrZeile[$position]);
            $position++;
            }   
        $parametsString = implode(", ", $paramets);

     $stmt = $Datenbank->prepare($query);
     $types = str_repeat('s', count($paramets));
     $stmt->bind_param($types, ...$paramets);
     $stmt->execute();
    }

if($tabellenname =="benutzer"){
    $query = "INSERT IGNORE INTO ".$tabellenname."($intospaltentext) VALUES ($fragezeichen);";
    $paramets = array();
    $position = 0;
    for($az=0;$az < $anzahlSpalten; $az++){
        if($az==1){                                      //Sonderfall: Name muss als leerer Text eingetragen sein 
                $paramets[] = "";
        }
        if($az==5){                                     /*Einmalpasswort erstellen */
            $laenge = rand(20,24);
            $temporaeresPW ="";
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!*,;+-_()[]:";
            $var_size = strlen($chars);
            for( $x = 0; $x < $laenge; $x++ ) {  
                $temporaeresPW= $temporaeresPW.$chars[ rand( 0, $var_size - 1 ) ];
            }
            $paramets[] = $temporaeresPW;
        }
        if($az!=5 && $az!=1)
        {
            $paramets[] = trim($arrZeile[$position]);
            $position++;
            }
        }     
        $parametsString = implode(", ", $paramets);

      $stmt = $Datenbank->prepare($query);
     $types = str_repeat('s', count($paramets));
     $stmt->bind_param($types, ...$paramets);
     $stmt->execute();
    }

}
}


//neue Einträge ermitteln //////////////////////////////////////////////////////////////////////////
$abfrage = $Datenbank->query("SELECT count(*) as Anzahl FROM ".$tabellenname.";");
$neueAnzahl = $abfrage->fetch_object()->Anzahl;
$differenz = ($neueAnzahl - $AnzahlEintraege);

	echo json_encode($differenz);
    exit;

}

}}
?>