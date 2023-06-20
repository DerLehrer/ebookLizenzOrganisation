$(document).ready(function() {
    loadtable();

    ////////// Upload-Aktion: Vorab-Check Größe, Typ ///////////

    $(':file').on('change', function () {
      $('#dateiInfo').html('');
      $("#formular").find("input[type=file]").each(function(index, field){
        for(var i=0;i<field.files.length;i++) {
          var file = field.files[i];
          if (file.size > 8192) {
            $('#dateiInfo').html('Die Datei ' + file.name + ' ist zu groß.<br>Die Dateigröße darf 8 MB nicht überschreiten.');
            return;
            }
            var ext = file.name.split('.').pop();
            if(ext != "csv"){
            $('#dateiInfo').html("Alle Dateien müssen in UTF-8 codierte CSV-Dateien sein.");
            return;
           };   
        }
    if(field.files.length<2){
      $('#dateiInfo').html('Die Datei ' + file.name + ' wurde geladen');
    }
    else{
    $('#dateiInfo').html(field.files.length + " Dateien geladen");
  }
  } )
})
})

/////////// DataTable aus Datenabruf erstellen ///////////////

function loadtable(){
    $.ajax({
    type: "GET",
    url: "../php/getBuecher.php",
    dataType : "json",
    success: function( data ) {
      str = '<table id="buchtabelle" class="table "  style="width:100%"><thead><tr><th></th><th>ID</th><th>Jahrgangsstufe</th><th>Titel</th><th>Autoren</th><th>Preis</th><th>Bestellungen</th><th>Codes</th></tr></thead><tbody>';
       $.each(data, function (index, value) {
           str = str + '<tr><td></td><td>'+value.Id+'</td><td>'+value.Klasse+'</td><td>'+value.Titel+'</td><td>'+value.Autoren+'</td><td>'+value.Preis+'</td><td>'+value.Bestellungen+'</td><td>'+value.Codes+'</td>';
       })
       str = str + '</tbody></table>';
       $('#output').html(str);
   } 
  });

/////////////// DataTable-Einstellungen ////////////////////////

setTimeout(function() {
  var table = $("#buchtabelle").DataTable( {
  
  columnDefs: [ {  orderable: true,
                   className: 'select-checkbox',
                   targets:   0    } ],
  order: [[ 1, 'asc' ]],
  autoWidth: true,
  paging: false,
  searching: true,
  info: false,
  select: true,
  language: {   lengthMenu: "Zeige _MENU_ Zeilen",
                zeroRecords: "Keine Einträge gefunden",
                info: "Seite _PAGE_ von _PAGES_",
                infoEmpty: "Keine Einträge vorhanden",
                infoFiltered: "(aus _MAX_ Einträgen)",
                paginate: { previous: "zurück",
                            next: "weiter"    },
                searchPlaceholder: 'Suche',
                search: ""  },
                
  buttons: [     {   text: 'Alle Einträge',
                      action: function () {  table.rows().select(); } ,
                      className: "btn btn-extra"  },
                {   text: 'Keine Einträge',
                      action: function () {  table.rows().deselect(); },   
                      className: "btn btn-extra" 
                } 
            ]
  });
  /////// 2. Buttongruppe /////////
  new $.fn.dataTable.Buttons( table, {
    buttons: {
        dom: {
        collection: {
            tag: 'aside'
        }    }
    },
    buttons:[   
    { extend: 'pdf', text: 'PDF',  className: "btn btn-secondary" },  
    { extend: 'excel', text: 'Excel',   className: "btn btn-secondary" },
    ]});

    /////// 3. Buttongruppe /////////
    new $.fn.dataTable.Buttons( table, {
      buttons: {
          dom: {
          collection: {
              tag: 'aside'
          }    }
      },
      buttons:[   
        {   text: "Einträge löschen",
            action: function (){  buchloeschen(table);  },
            className: "btn btn-secondary" 
        },
        {   text: "Einträge hinzufügen",
            action: function (){ hochladen(table); },
            className: "btn btn-secondary" 
        },
        {   text: "Einträge ändern",
            action: function (){    
                        var data = table.rows( { selected: true } ).data();
                        var anzahl = table.rows( { selected: true } ).count();
                        var string = "";
                        for(let i=0; i<anzahl;i++){ string = string + data[i][3] + "-"; }
                        alert(string);         }  ,
                        className: "btn btn-secondary" 
      }      
      ]});
  
  table.buttons( 0, null ).containers().addClass('pufferLinks').appendTo( "#buttonleiste" );
  table.buttons( 1, null ).containers().addClass('btn-group flex-wrap pufferRechts').appendTo( "#buttonleiste" );
  table.buttons( 2, null ).containers().addClass('btn-group flex-wrap pufferRechts').appendTo( "#buchtabelle_filter" );

  $(':button').removeClass('dt-button');
  $(':button').removeClass('buttons-excel');
  $(':button').removeClass('buttons-html5 ');

  $("#buchtabelle tbody").on('click', 'tr', function () {  $(this).toggleClass('selected');   });
  
},50); }

//////////////// Modale Dialoge ////////////////////////////
function buchloeschen(table) {
    $( "#dialog-buchloeschen" ).dialog({
        open: function() {
            $(".ui-dialog-titlebar-close").hide();
        },
      dialogClass:'warnend',
      width: "auto",
      resizable: false,
      modal: true,
      position: {my: "top", at: "top", of: $("#buchtabelle")},
      buttons: [
        {
        text: "Ja - alles löschen",
        icon: "ui-icon-check",
        class: "btn-warnend",
        click: function() {
            var data = table.rows( { selected: true } ).data();
            var anzahl = table.rows( { selected: true } ).count();
            var loeschende = [];
            for(let i=0; i<anzahl;i++){
                  alert(loeschende[i]=data[i][1]); 
              };
            loadtable();
            $( this ).dialog( "close" );
        }}
        ,
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

  function hochladen(table) {
    $( "#dialog-hochladen" ).dialog({
        open: function() {
            $(".ui-dialog-titlebar-close").hide();
        },
      dialogClass:'entspannt',
      width: "auto",
      resizable: false,
      modal: true,
      position: {my: "top", at: "top", of: $("#buchtabelle")},
      buttons: [
        {
        text: "Hochladen",
        icon: "ui-icon-check",
        class: "btn-success",
        click: function() { //Datei muss als file_data an Formulardaten angehängt werden
                            var file_data = $('#inputFile').prop('files')[0];   
                            var form_data = new FormData();                  
                            form_data.append('file', file_data);
                            $.ajax({
                              type: "POST",
                              url: "dateiInDB.php",
                              data: form_data,       
                              contentType: false,
                              cache: false,
                              processData:false,
                              dataType : "json",
                              error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                              },
                              success: function( data ) {
                              table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                              table.buttons( 1, null ).remove();
                              loadtable();  
                              $('#dateiInfo').html("");
                              $( "#dialog-hochladen" ).dialog( "close" );                    
                              }  
                            })
                          }
        },
        {
        text: "Abbrechen",
        icon: "ui-icon-close",
        class: "btn-success",
        click: function() {
          $( this ).dialog( "close" );
        }
        }
      ]
    },
    
    
    );
  } ; 