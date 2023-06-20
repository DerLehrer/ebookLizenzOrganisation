<?php
header('Content-Type: application/json; charset=utf-8');
require_once("session.php");
require_once("db_zugriff.php");
setlocale(LC_ALL, "de_DE.UTF8");

if ( isset($_SESSION["benutzer"]) && time() < $_SESSION["ablaufzeit"]) {
if($_SESSION["benutzer"]=="Verwalter"){

$erfolgreicheZuordnungen = 0;
$uebrigeCodes = 0;
$verbleibendeBestellungen = 0;


$alleBuecher = $Datenbank->query("SELECT Buch FROM buch");
    if ($alleBuecher->num_rows > 0) {
        while ($row = $alleBuecher->fetch_object()) {
            $r = $row->Buch;
            $buchCodes = $Datenbank->query("SELECT Codes, Anzahl_max - Anzahl_verwendungen AS verfuegbareAnzahl FROM codes WHERE BuchID = '".$r."' AND Anzahl_verwendungen<Anzahl_max ");
            $buchBestellungen = $Datenbank->query("SELECT BestellerID FROM bestellung WHERE BuchID =  '".$r."' AND Code IS NULL;");
            $anzahlCodes = $buchCodes->num_rows;
            $anzahlVergebbarerCodes = 0;
            $alleCodeObjekte = array();
            // ermittle Gesamtzahl der fuer ein Buch verfuegbaren Codes
            for ($i = 0; $i < $anzahlCodes; $i++) {
                $alleCodeObjekte[$i] = $buchCodes->fetch_object();
                $vA = $alleCodeObjekte[$i]->verfuegbareAnzahl;
                $anzahlVergebbarerCodes = $anzahlVergebbarerCodes+$vA;
            }
            // ermittle Gesamtzahl nicht bedienter Bestellungen fuer das Buch
            $anzahlBestellungen = $buchBestellungen->num_rows;
            
            // wenn mehr oder gleich viele Codes wie Bestellungen vorliegen, ordne der Liste aller Bestellungen einen Code zu und schreibe dies in bestellungen - ist die Liste der Bestellungen groesser, ordne der Liste der Codes jeweils einen Besteller zu und speichere das in bestellungen
            if ($anzahlVergebbarerCodes >= $anzahlBestellungen && $anzahlVergebbarerCodes > 0) {
                $uebrigeCodes = $uebrigeCodes + $anzahlVergebbarerCodes - $anzahlBestellungen;            
                for ($i = 0; $i < $anzahlBestellungen; $i++) {
                   $anzahlDiesesObjekts = $alleCodeObjekte[$i]->verfuegbareAnzahl;
                   //verwende mehrfach nutzbaren Code mehrmals
                     while($anzahlDiesesObjekts > 0 && $anzahlBestellungen > 0){
                        $bID = $buchBestellungen->fetch_object()->BestellerID;  //kein Iterator fuer Bestellungen noetig! Immer den naechsten Datensatz verwenden :-)
                        $CodeDiesesObjekts = $alleCodeObjekte[$i]->Codes;
                        $Datenbank->query("UPDATE bestellung SET Code = '".$CodeDiesesObjekts."' WHERE BuchId = '".$r."' AND BestellerID = '".$bID."';");
                        $Datenbank->query("UPDATE codes SET Anzahl_verwendungen = Anzahl_verwendungen+1 WHERE Codes = '".$CodeDiesesObjekts."';");
                        $anzahlDiesesObjekts--;
                        $anzahlBestellungen--;
                        $erfolgreicheZuordnungen++;
                    }
                    $anzahlBestellungen++;
                    }
            } else {
                $verbleibendeBestellungen = $verbleibendeBestellungen + $anzahlBestellungen - $anzahlVergebbarerCodes;
                for ($i = 0; $i < $anzahlVergebbarerCodes; $i++) {
                    $anzahlDiesesObjekts = $alleCodeObjekte[$i]->verfuegbareAnzahl;
                    //verwende mehrfach nutzbaren Code mehrmals
                      while($anzahlDiesesObjekts > 0 && $anzahlBestellungen > 0){
                         $bID = $buchBestellungen->fetch_object()->BestellerID;  //kein Iterator fuer Bestellungen noetig! Immer den naechsten Datensatz verwenden :-)
                         $CodeDiesesObjekts = $alleCodeObjekte[$i]->Codes;
                         $Datenbank->query("UPDATE bestellung SET Code = '".$CodeDiesesObjekts."' WHERE BuchId = '".$r."' AND BestellerID = '".$bID."';");
                         $Datenbank->query("UPDATE codes SET Anzahl_verwendungen = Anzahl_verwendungen+1 WHERE Codes = '".$CodeDiesesObjekts."';");
                         $anzahlDiesesObjekts--;
                         $anzahlBestellungen--;
                         $erfolgreicheZuordnungen++;
                     }
                     $anzahlBestellungen++;
                     }
            }
            
        }

        $returnwert = [$erfolgreicheZuordnungen, $anzahlVergebbarerCodes, $verbleibendeBestellungen];
        echo json_encode($returnwert);
    }
    else{
    echo json_encode("_000_");
}
} 
else{
    echo json_encode("timeout");
}

}

?>

