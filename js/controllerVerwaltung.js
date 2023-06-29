$(document).ready(function() {
    loadtable();
      /*        ö = \u00F6  Ö  = \u00D6 
                ä = \u00E4  Ä  = \u00C4
                ü = \u00FC  Ü  = \u00DC
                ß = \u00DF  
                */    

 ////////// Upload-Aktion: Vorab-Check Größe, Typ ///////////

    $(':file').on('change', function () {
      //$('#dateiInfo').html('');
      $("#formular").find("input[type=file]").each(function(index, field){
        for(var i=0;i<field.files.length;i++) {
          var file = field.files[i];
          if (file.size > 81920) {
            $('#dateiInfo').html('Die Datei ' + file.name + ' ist zu groß.<br>Die Dateigr\u00F6ße darf 8 MB nicht \u00FCberschreiten.');
            return;
            }
            var ext = file.name.split('.').pop();
            if(ext != "csv"){
            $('#dateiInfo').html("Alle Dateien m\u00FCssen in UTF-8 codierte CSV-Dateien sein.");
            return;
           };   
        }
    if(field.files.length<2){
      $('#dateiInfo').html('Die Datei ' + file.name + ' wurde ausgew\u00E4hlt');
    }
    else{
    $('#dateiInfo').html(field.files.length + " Dateien ausgew\u00E4hlt");
  }
  } )
})

$("#Impressum").click(function(){window.location.assign("../impressum_g.html");})
$("#Datenschutz").click(function(){window.location.assign("../datenschutzerklaerung_g.html");})  

})

/////////// DataTable aus Datenabruf erstellen ///////////////

function loadtable(){
   if($('#seitenid').text()=="buecher"){
    $.ajax({
    type: "GET",
    url: "../php/getBuecher.php",
    dataType : "json",
    success: function( data ) {
      str = '<table id="seitentabelle" class="table "  style="width:100%"><thead><tr><th></th><th>ID</th><th>Jahrgangsstufe</th><th>Fach</th><th>Verlag</th><th>Preis</th><th>Bestellungen</th><th>Codes</th></tr></thead><tbody>';
       $.each(data, function (index, value) {
        let anzahl = value.Codes;
        if(value.Anzahl_max > value.Codes){
          anzahl = value.Anzahl_max;
        }
       //editierbare Felder müssen class = "editable" eingestellt werden
       str = str + '<tr><td></td><td>'+value.Buch+'</td><td>'+value.Stufe+'</td><td>'+value.Fach+'</td><td>'+value.Verlag+'</td><td class = "editable">'+value.Preis+'</td><td>'+value.Bestellungen+'</td><td>'+anzahl+'</td>';
       })
       str = str + '</tbody></table>';
       $('#output').html(str);
   }
  })
  }
  else if($('#seitenid').text()=="codes"){
    $.ajax({
      type: "GET",
      url: "../php/getCodes.php",
      dataType : "json",
      success: function( data ) {
        //Primärschlüsselspalte wird versteckt - ist aber inhaltlich zugänglich
        str = '<table id="seitentabelle" class="table "  style="width:100%"><thead><tr><th></th><th>Buch</th><th>Codes</th><th>Verwendung</th><th>Menge</th></tr></thead><tbody>';
         $.each(data, function (index, value) {
          let verwendung = "- nicht zugeordnet -";
          // für mehrfach zu verwendende Codes
          if(value.Anzahl_max > 1){
            verwendung = value.Anzahl_verwendungen+'/'+value.Anzahl_max;
          }
          // für einmalig zu verwendende Codes
          else if(value.BestellerId != null){
            verwendung = value.BestellerId;
          }
          //editierbare Felder müssen class = "editable" eingestellt werden
            str = str + '<tr><td></td><td>'+value.BuchT+'</td><td>'+value.Codes+'</td><td>'+verwendung+'</td><td class = "editable">'+value.Anzahl_max+'</td>';
        })
         str = str + '</tbody></table>';
         $('#output').html(str);
     } 
    })
  }
  else if($('#seitenid').text()=="nutzer"){
    $.ajax({
      type: "GET",
      url: "../php/getNutzer.php",
      dataType : "json",
      success: function( data ) {
        let anzahl = 0;
        let eingeladene = 0;
        str = '<table id="seitentabelle" class="table "  style="width:100%"><thead><tr><th></th><th>Email-Adresse</th><th>Sch&uuml;ler</th><th>Klasse</th><th>eingeladen</th><th>hat sich registriert</th><th>Bestellkosten</th></tr></thead><tbody>';
         $.each(data, function (index, value) {
          let reg = "nicht registriert";
          let ein = "Nein";
          anzahl++;
          if(value.Gesetzt==1){reg="registriert"}
          if(value.Eingeladen==1){ein = "Ja"; eingeladene++;}
          //editierbare Felder müssen class = "editable" eingestellt werden
          str = str + '<tr><td></td><td name = "mail" class = "editable">'+value.Email+'</td><td name = "vorname" class="editable">'+value.SchuelerVname+' '+value.SchuelerNname+'</td><td id=3 class = "editable">'+value.Klasse+'</td><td id=4 class = "editable">'+ein+'</td><td id=5>'+reg+'</td><td id=6>'+value.Kosten+'</td>';
         })
         str = str + '</tbody></table>';
         hinw = ''+eingeladene+' von ' +anzahl+ ' Nutzern wurden eingeladen<p>';
         $('#output').html(str);
         $('#hinweise').html(hinw);
     } 
    })
  }
  else if($('#seitenid').text()=="bestellungen"){
    $.ajax({
      type: "GET",
      url: "../php/getBestellungen.php",
      dataType : "json",
      success: function( data ) {
        str = '<table id="seitentabelle" class="table "  style="width:100%"><thead><tr><th></th><th>Besteller</th><th>Buch</th><th>Datum</th><th>Code</th></tr></thead><tbody>';
        $.each(data, function (index, value) {
        let codestring = value.Code;
        if(value.Code == null){ codestring = "noch kein Code";  }
        str = str + '<tr><td></td><td>'+value.BestellerID+'</td><td>'+value.BuchID+'</td><td>'+value.Datum+'</td></td><td>'+codestring+'</td>';
       })
        str = str + '</tbody></table>';
         $('#output').html(str);
     } 
    })
  }
  else if($('#seitenid').text()=="einstellungen"){
    $.ajax({
      type: "GET",
      url: "../php/getEinstellungen.php",
      dataType : "json",
      success: function( data ) {
        str = '<table class="etable" id="einstellungstabelle"><tbody>';
        $.each(data, function (index, value) {
          let sperrdatum = ""
        str = str + '<tr><td >Bestellende</td><td><input class ="einput"  type="date" id="inE1" value="'+value.Sperre+'"></input></td></tr><tr><td >Schulname</td><td><input class ="einput" type="text" id="inE2" value="'+value.Schulname+'"></input></td></tr><tr><td>Direktor</td><td><input class ="einput" type="text" id="inE3" value="'+value.Direktor+'"></input></td></tr><tr><td>Strasse</td><td><input class ="einput"  type="text"  id="inE4" value="'+value.Strasse+'"></input></td></tr>        <tr><td>PLZ</td><td><input class ="einput"  type="text" id="inE5" value="'+value.PLZ+'"></input></td></tr>        <tr><td>Ort</td><td><input class ="einput" type="text" id="inE6" value="'+value.Ort+'"></input></td></tr>        <tr><td>Verwalter</td><td><input class ="einput" type="text"  id="inE7" value="'+value.Admin+'"></input></td></tr>        <tr><td>Verwalter-Email</td><td><input class ="einput" type="text" id="inE8" value="'+value.Email+'"></input></td></tr><tr><td>Einladungstext</td><td ><div class ="einput" style="color:darkgrey; font-size:0.9em"><textarea class ="einput" id="inE9" type="text"  rows="6">'+value.Einladung+'</textarea><br>[individueller Link]<br>Mit freundlichen Grüßen,<br>i.A.<br>'+value.Admin+'</div></td></tr>';
       }) 
       str = str + '<tr><td></td><td style="text-align:center"><button class="btn btn-secondary" onclick="einstellungenSpeichern()">Einstellungen speichern</button>&nbsp;<button class="btn btn-secondary" onclick="sichern()"">Daten<br>sichern</button>&nbsp;<button class="btn btn-mainaction" onclick="zuruecksetzenSJ()">Datenspeicher zurücksetzen</button></td></tr>'
        str = str + '</tbody></table>';
         $('#output').html(str);
     } 
    })
  }
  ;


/////////////// DataTable-Einstellungen ////////////////////////

setTimeout(function() {
  if($('#output').html()=="&nbsp;"){
    $('#output').html("Fehler: Die Daten konnten nicht geladen werden.");
  }
  else{
  var table = $("#seitentabelle").DataTable( {
  columnDefs: [ {  orderable: true,
                   className: 'select-checkbox',
                   targets:   0    } ,
                  {
                   targets: '_all',
                   //Datumsangaben ermöglichen
                   render: function (data, type, row) {
                     if (type === 'display') {
                       if(isNaN(data) && moment(data, 'YYYY-MM-DD', true).isValid())
                       {
                           return moment(data).format('DD.MM.YYYY');
                       }
                     }
                     return data;
                 }
                }
                  ],
  order: [[ 1, 'asc' ]],
  autoWidth: true,
  paging: false,
  searching: true,
  info: true,
  select: {
    info: true,
    style: 'multi',
    },
  language: {   lengthMenu: "Zeige _MENU_ Zeilen",
                select: {
                  rows: {
                   _: "%d Eintr\u00E4ge ausgew\u00E4hlt",
                   0: "",
                   1: "1 Eintrag ausgew\u00E4hlt"
                  }
                },
                zeroRecords: "Keine Eintr\u00E4ge gefunden",
                info: "",
                infoEmpty: "",
                infoFiltered: "",
                paginate: { previous: "zurück",
                            next: "weiter"    },
                searchPlaceholder: 'Suche',
                search: ""  },                
  buttons: [     {   text: 'Alle Eintr\u00E4ge',
                      action: function () {  table.rows().select();
                        table.rows().every( function () {             /*noetig zur korrekten Sortierung nach Selektion*/
                          table.cell(this,0).data("&nbsp;");         
                        } );
                      } ,
                      className: "btn btn-extra"  },
                {   text: 'Keine Eintr\u00E4ge',
                      action: function () {  table.rows().deselect(); 
                        table.rows().every( function () {             /*noetig zur korrekten Sortierung nach Selektion*/
                        table.cell(this,0).data("");
                        } );
                      },   
                      className: "btn btn-extra" 
                } 
            ]
  });
  /////// 2. Buttongruppe /////////

  var buttons2 = [];
  buttons2.push({ extend: 'pdf', text: 'PDF', title: 'Datenexport eBook-Verwaltung', className: "btn btn-secondary" });
  buttons2.push({ extend: 'excel', text: 'Excel',  title: 'Datenexport eBook-Verwaltung', className: "btn btn-secondary" , exportOptions:{columns: ':gt(0)' }});
  if($('#seitenid').text()=="codes"){buttons2.push({   text: "Codes zuordnen", action: function(){zuordnen();}, className: "btn btn-mainaction" });}
  if($('#seitenid').text()=="nutzer"){buttons2.push({ text: "Nutzer einladen", action: function(){einladen();}, className: "btn btn-mainaction" });}
  if($('#seitenid').text()=="einstellungen"){buttons2.push({   text: "Alle Daten l\u00F6schen", action: function(){zuruecksetzenSJ();}, className: "btn btn-mainaction" });}

  new $.fn.dataTable.Buttons( table, {
    buttons: {
        dom: {
        collection: {
            tag: 'aside'
        }    }
    },
    buttons:buttons2            //mit Array, damit konditional veränderbar
    });

    /////// 3. Buttongruppe /////////
    var buttons3 = [];

   if($('#seitenid').text()!="bestellungen" && $('#seitenid').text()!="einstellungen" ){
    buttons3.push('spacer');
      buttons3.push({   text: "Eintr\u00E4ge l\u00F6schen", action: function(){loeschen();},className: "btn btn-secondary linksrund" });
      buttons3.push( {  text: "Eintr\u00E4ge erg\u00E4nzen", action: function (){ hochladen(table); }, className: "btn btn-secondary" });
      buttons3.push( { text: "Eintr\u00E4ge \u00E4ndern", name: "aenderung", action: function(){aendern();}, className: "btn btn-secondary" });
    }
    else if ($('#seitenid').text()=="bestellungen"){
      buttons3.push({   text: "Eintr\u00E4ge l\u00F6schen", action: function(){loeschen();},className: "btn btn-secondary" });
    }
    }
   
    new $.fn.dataTable.Buttons( table, {
      buttons: {
          dom: {
          collection: {
              tag: 'aside'
          }    }
      },
      buttons:buttons3            //mit Array, damit konditional veränderbar
      });


  table.buttons( 0, null ).containers().addClass('pufferLinks').appendTo( "#buttonleiste" );
  table.buttons( 1, null ).containers().addClass('btn-group flex-wrap pufferRechts').appendTo( "#buttonleiste" );
  table.buttons( 2, null ).containers().addClass('btn-group flex-wrap pufferRechts').appendTo( "#seitentabelle_filter" );

  $(':button').removeClass('dt-button');
  $(':button').removeClass('buttons-excel');
  $(':button').removeClass('buttons-html5 ');

  $("#seitentabelle tbody").on('click', 'tr', function () { 
    $(this).toggleClass('selected');   
    if(table.cell(this,0).data()=="&nbsp;"){table.cell(this, 0).data("");}
    else{table.cell(this,0).data("&nbsp;");}
  });
},200); }

///////////////////////////////////////////////////////////
function einladen(){
  $( "#dialog-einladen" ).dialog({
    //show: 'fade',
    //hide: 'fade',
    open: function() {
        $(".ui-dialog-titlebar-close").hide();
    },
  dialogClass:'mahnend',
  width: "auto",
  resizable: false,
  modal: true,
  position: {my: "top", at: "top", of: $("#seitentabelle")},
  buttons: [
    {
    text: "Einladung verschicken",
    icon: "ui-icon-check",
    class: "btn-mahnend",
    click: function() {   
      var table = $("#seitentabelle").DataTable();   
  $.ajax({
    type: "GET",
    url: "einladen.php",
    cache: false,
    dataType : "json",
    error: function (xhr, ajaxOptions, thrownError) {
      //alert(thrownError);
    },
    success: function( data ) {
    if(data==0){
      alert("Es wurden keine Mails verschickt.");
    }
    else{
      alert("Es wurden "+data+" Einladungs-Mails versandt. \nAus Spamschutzgr\u00FCnden k\u00F6nnen maximal 40 Mails auf einmal verschickt werden.");
     table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
     table.buttons( 1, null ).remove();
     loadtable();  
    }
    }})
        $( this ).dialog( "close" );
      }
    },
    {
      text: "Abbrechen",
      icon: "ui-icon-close",
      class: "btn-mahnend",
      click: function() {
        $( this ).dialog( "close" );
      }
  }]
})};

//erstellt einen db-Dump //////////////////////////////////////////////////////////////////////////////////////////////
function sichern(){
  $.ajax({
    type: "GET",
    url: "backup.php",
    cache: false,
    dataType : "json",
    error: function (xhr, ajaxOptions, thrownError) {
      //alert(thrownError);
      alert("Fehler");
    },
    success: function( data ) {
         alert("Es wurde ein Backup im Verzeichnis "+data+" erstellt.");
    }
    })
};

//Sichern der Einstellungsdaten //////////////////////////////////////////////////////////////////////////////////////////////
function einstellungenSpeichern(){
  var updateDaten = [];
updateDaten[0]=$('#seitenid').text();
for(var i=1; i<10;i++){
  let element = '#inE'+i;
  updateDaten[i]= $(element).val();
} 

  var jsonString = JSON.stringify(updateDaten);
          $.ajax({
            type: "POST",
            url: "aendern.php",
            data: {auswahl : jsonString}, 
            cache: false,
            dataType : "json",
            success: function( data ) {
              alert("Einstellungen gespeichert.");
              location.reload();
              },
              error: function (data){alert("Ein Fehler ist aufgetreten.")}
            })

      };

//////////////// Modale Dialoge ////////////////////////////
function zuordnen() { 
  $( "#dialog-zuordnen" ).dialog({
    //show: 'fade',
    //hide: 'fade',
    open: function() {
        $(".ui-dialog-titlebar-close").hide();
    },
  dialogClass:'mahnend',
  width: "auto",
  resizable: false,
  modal: true,
  position: {my: "top", at: "top", of: $("#seitentabelle")},
  buttons: [
    {
    text: "Zuordnen",
    icon: "ui-icon-check",
    class: "btn-mahnend",
    click: function() {   
      var table = $("#seitentabelle").DataTable();      
      $.ajax({
        type: "GET",
        url: "codesZuordnen.php",
        cache: false,
        dataType : "json",
        error: function (xhr, ajaxOptions, thrownError) {
          //alert(thrownError);
        },
        success: function( data ) {
        if(data=="timeout"){
          window.location.replace("../anmeldung.html");
        }
        else if(data=="_000_"){
          alert("Es sind keine B\u00FCcher vorhanden.");
        }
        else{
    	    alert("Es wurden "+data[0]+" Codes erfolgreich zugeordnet.\n"+data[1]+" vorhandene Codes wurden noch nicht vergeben.\n"+data[2]+" Bestellungen haben noch keinen Code erhalten.");
         table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
         table.buttons( 1, null ).remove();
         loadtable();  
        }
        }})
        
        $( this ).dialog( "close" );
      }
    },
    {
      text: "Abbrechen",
      icon: "ui-icon-close",
      class: "btn-mahnend",
      click: function() {
        $( this ).dialog( "close" );
      }
  }]})};


function hochladen(table) {
  var seitenid = $('#seitenid').text();
  $( "#dialog-hochladen" ).dialog({
      //show: 'fade',
      //hide: 'fade',
      open: function() {
          $(".ui-dialog-titlebar-close").hide();
      },
    dialogClass:'entspannt',
    width: "auto",
    resizable: false,
    modal: true,
    position: {my: "top", at: "top", of: $("#seitentabelle")},
    buttons: [
      {
      text: "Hochladen",
      icon: "ui-icon-check",
      class: "btn-success",
      click: function() { //Datei muss als form_data an Formulardaten angehängt werden
                          var table = $("#seitentabelle").DataTable();                  
                          let form_data = new FormData(); 
                          $("#formular").find("input[type=file]").each(function(index, field){
                            for(var i=0;i<field.files.length;i++) {
                              let datei = field.files[i];
                              form_data.append('zeugs[]', datei );
                            }
                          })
                          form_data.append('sid', seitenid );
                          $.ajax({
                            type: "POST",
                            url: "dateiInDB.php",
                            data: form_data,
                            contentType: false,
                            cache: false,
                            processData:false,
                            dataType : "json",
                            error: function (xhr, ajaxOptions, thrownError) {
                              //alert(thrownError);
                            },
                            success: function( data ) {
                            if(data=="_8888_"){
                              $('#dateiInfo').html("<div style='color: red'>Eine Datei hat die falsche Anzahl an Spalten.<br>Als Trennzeichen muss ';' eingestellt sein.</div>");
                              table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                              table.buttons( 1, null ).remove();
                              loadtable();  
                            }
                            else if(data=="_7777_"){
                              $('#dateiInfo').html("<div style='color: red'>Die Datei muss in UTF-8-Codierung gespeichert sein.</div>");
                              table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                              table.buttons( 1, null ).remove();
                              loadtable();  
                            }
                            else{
        
                              $('#dateiInfo').html("Es wurden "+ data + " Einträge hinzugefügt");
                              $(':file').val('');
                            table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                            table.buttons( 1, null ).remove();
                            loadtable();  
                            }  
                          }
                          })
                        }
      },
      {
      text: "Schlie\u00DFen",
      icon: "ui-icon-close",
      class: "btn-success",
      click: function() {
        $(':file').val('');
        $('#dateiInfo').html("");
        $( this ).dialog( "close" );
      }
      }
    ]
  }, );} ; 

  function zuruecksetzenSJ(){
    $( "#dialog-reset" ).dialog({
      //show: 'fade',
      //hide: 'fade',
      open: function() {
          $(".ui-dialog-titlebar-close").hide();
      },
    dialogClass:'warnend',
    width: "auto",
    resizable: false,
    modal: true,
    position: {my: "top", at: "top", of: $("#einstellungstabelle")},
    buttons: [
        {  text: "Alle",
        icon: "ui-icon-check",
        class: "btn-warnend",
        click: function() {   $.ajax({
                              type: "GET",
                              url: "schuljahrloeschen.php",
                              cache: false,
                              dataType : "json",
                              error: function (xhr, ajaxOptions, thrownError) {
                                          //alert(thrownError);
                                          alert("Es gab einen Fehler - bitte checken, ob noch Daten vorhanden sind.");
                                        },
                                        success: function( data ) {
                                            alert("Alle Daten des Schuljahres wurden gel\u00F6scht");
                                          }
                                })
                               $( this ).dialog( "close" );
                            }
         },
         {
              text: "Abbrechen",
              icon: "ui-icon-close",
              class: "btn-warnend",
              click: function() {
              $( this ).dialog( "close" );
             }
         }
        ]
      }, );} ; 

  // INLINE-AENDERUNGEN //////////////////////////////////////////////////////////////////////////////////////////////////////////////
  var clickedRow;                                                       //Global nötig, damit wiederholter Bezug darauf möglich (Ändern und Speichern)
  var primSchl;

  function aendern(){
    var table = $("#seitentabelle").DataTable();   
    if(table.button('aenderung:name').text() != "Werte speichern"){
      //Änderbare Werte auf eine Zeile beschränken
      clickedRow = table.row( { selected: true });
      table.rows().deselect();   
      clickedRow.select();
      var zeilendaten = clickedRow.data();                          
      primSchl = zeilendaten[1];
      $(clickedRow.node()).find('td').each(function () {      
      /*  if ($(this).hasClass('gross')) {
          var html = fnCreateTextArea($(this).html(), "updatebox");    
           $(this).html($(html))    
     } 
      else */ if ($(this).hasClass('editable')) {
            var html = fnCreateTextBox($(this).html(), "updatebox");    
             $(this).html($(html))    
     }    
     table.button('aenderung:name').text("Werte speichern");
   
 });     
     }
     else{
      var openedTextBox = $(clickedRow.node()).find('input');  
     $.each(openedTextBox, function (k, $cell) {    
      //Ändere Werte in der angezeigten Tabelle
       fnUpdateDataTableValue($cell, $cell.value);    
       $(openedTextBox[k]).closest('td').html($cell.value);    
     });
    clickedRow.select(); 
   table.button('aenderung:name').text("Werte aendern");
   var dat = clickedRow.data();
   //Ändere Werte in der DB-Tabelle
   updateDB(dat, primSchl);  
     }
}

/*  function fnCreateTextArea(value, fieldprop) {    
return '<textarea data-field="' + fieldprop + '" type="text" cols="50" rows="6">' + value + '</textarea>';	
}   */

function fnCreateTextBox(value, fieldprop) {    
  return '<input data-field="' + fieldprop + '" type="text" value="' + value + '" size = "13"></input>';    
}  

function fnUpdateDataTableValue($inputCell, value) {  
  var dataTable = $('#seitentabelle').DataTable();    
  var rowIndex = dataTable.row($($inputCell).closest('tr')).index(); 
  var cellIndex = $($inputCell).closest('td').index()    
  dataTable.cell(rowIndex,cellIndex).data(value).draw(); 
  
}  

function updateDB(data, primSchl) {
  var seitenid = $('#seitenid').text();
  var table = $("#seitentabelle").DataTable();
 // var data = table.rows( { selected: true } ).data();
  var updateDaten = [];
  updateDaten[0]=seitenid;
  updateDaten[1]=primSchl;
  primSchl = "";
     for(let i=2; i<=data.length;i++){                       //erster Eintrag (Checkbox) weglassen
            updateDaten[i]=data[i-1];          };
           if(updateDaten.length > 1){
            var jsonString = JSON.stringify(updateDaten);
            $.ajax({
              type: "POST",
              url: "aendern.php",
              data: {auswahl : jsonString}, 
              cache: false,
              dataType : "json",
              success: function( data ) {
                table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                table.buttons( 1, null ).remove();
                loadtable();
                },
                error: function (data){alert("Ein Fehler ist aufgetreten.")}
              })
              }
              else{
            }
        }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////


//Ausgewaehlte Daten loeschen ////////////////////////////////////////////////////////////////////////////////////////////////////////////#

function loeschen(){
  if($('#seitenid').text()=='codes' || $('#seitenid').text()=='bestellungen' ){zweiprimloeschen(); }          // Primaerschluessel aus zwei Spalten zu berücksichtigen
  else{einfachloeschen();}  
}

function zweiprimloeschen() {
  var seitenid = $('#seitenid').text();
    $( "#dialog-loeschen" ).dialog({
        open: function() {
            $(".ui-dialog-titlebar-close").hide();
        },
      dialogClass:'warnend',
      width: "auto",
      resizable: false,
      modal: true,
      position: {my: "top", at: "top", of: $("#seitentabelle")},
      buttons: [
        {
        text: "Ja - alles l\u00F6schen",
        icon: "ui-icon-check",
        class: "btn-warnend",
        click: function() {
          var table = $("#seitentabelle").DataTable();
          var data = table.rows( { selected: true } ).data();
          var anzahl = table.rows( { selected: true } ).count();
          var loeschende = [];
          loeschende[0]=seitenid;
          var platz=1;
          // zusammengesetzter Primaerschluessel benoetigt beide Werte
          for(let i=0; i<anzahl;i++){ 
            loeschende[platz]=data[i][1]; 
            loeschende[platz+1]=data[i][2]; 
            platz=platz+2;
          };
          if(loeschende.length > 1){
            var jsonString = JSON.stringify(loeschende);
            $.ajax({
              type: "POST",
              url: "entfernen.php",
              data: {auswahl : jsonString}, 
              cache: false,
              dataType : "json",
              success: function( data ) {
                alert(data + " Eintr\u00E4ge wurden gel\u00F6scht.");
                table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                table.buttons( 1, null ).remove();
                loadtable();
                }
              })
            $( this ).dialog( "close" );
              }
              else{
         
          }
        },
          },
        {
        text: "Abbrechen",
        icon: "ui-icon-close",
        class: "btn-warnend",
        click: function() {
          $( this ).dialog( "close" );
        }
        }
      ]
    },
      );
  } ; 

  function einfachloeschen() {
    var seitenid = $('#seitenid').text();
      $( "#dialog-loeschen" ).dialog({
          open: function() {
              $(".ui-dialog-titlebar-close").hide();
          },
        dialogClass:'warnend',
        width: "auto",
        resizable: false,
        modal: true,
        position: {my: "top", at: "top", of: $("#seitentabelle")},
        buttons: [
          {
          text: "Ja - alles l\u00F6schen",
          icon: "ui-icon-check",
          class: "btn-warnend",
          click: function() {
              var table = $("#seitentabelle").DataTable();
              var data = table.rows( { selected: true } ).data();
              var anzahl = table.rows( { selected: true } ).count();
              var loeschende = [];
              loeschende[0]=seitenid;
              for(let i=0; i<anzahl;i++){ loeschende[i+1]=data[i][1]; };
              if(loeschende.length > 1){
                var jsonString = JSON.stringify(loeschende);
              $.ajax({
                type: "POST",
                url: "entfernen.php",
                data: {auswahl : jsonString}, 
                cache: false,
                dataType : "json",
                success: function( data ) {
                  alert(data + " Eintr\u00E4ge wurden gel\u00F6scht.");
                  table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                  table.buttons( 1, null ).remove();
                  loadtable();
                  }
                })
              $( this ).dialog( "close" );
              }
              else{
              }},
              },
          {
          text: "Abbrechen",
          icon: "ui-icon-close",
          class: "btn-warnend",
          click: function() {
            $( this ).dialog( "close" );
          }
          }
        ]
      },
        );
    } ; 

 