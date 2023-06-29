$(document).ready(function() {
    var zustaendig =""
    var sperrdatum = null;
    getZustaendigen();
    loadtable();
      /*        ö = \u00F6  Ö  = \u00D6 
                ä = \u00E4  Ä  = \u00C4
                ü = \u00FC  Ü  = \u00DC
                ß = \u00DF  
                */
                $("#Impressum").click(function(){window.location.assign("../impressum_b.html");})
                $("#Datenschutz").click(function(){window.location.assign("../datenschutzerklaerung_b.html");})      
})

/////////// DataTable aus Datenabruf erstellen ///////////////

function loadtable(){
    $.ajax({
    type: "GET",
    url: "../php/getMeineBestellungen.php",
    dataType : "json",
    success: function( data ) {
     
      let kosten = 0.00;
      str = '<table id="seitentabelle" class="table" style="width:100%"><thead><tr><th>Auswahl</th><th>Buch</th><th>Jahrgangsstufe</th><th>Fach</th><th>Verlag</th><th>Preis</th><th>Ihr Code</th></tr></thead><tbody>';
       $.each(data, function (index, value) {
        let code = "-";
        if(value.Bestellt == '&nbsp;'){kosten = 1*kosten + 1*value.Preis}
        if(value.Code != null){code = value.Code;}
        str = str + '<tr><td>'+value.Bestellt+'</td><td>'+value.Buch+'</td><td>'+value.Stufe+'</td><td>'+value.Fach+'</td><td>'+value.Verlag+'</td><td>'+value.Preis+' €</td><td>'+code+'</td>';
       })
       str = str + '</tbody></table>';
       $('#output').html(str);
       let hinw = "";
       if(sperrdatum != null && sperrdatum != "0000-00-00"){
        let jahr = sperrdatum.substring(0,4);
        let monat = sperrdatum.substring(5,7);
        let tag = sperrdatum.substring(8,10);
       hinw += "<div style='color:red; font-weight:bold'>Bestellfrist bis "+tag+"."+monat+"."+jahr+"<br></div>";
       }
       hinw += "<div style='color:#007bff; font-weight:normal'>Sie haben ebooks im Wert von "+kosten+" Euro bestellt.</div><p>";
 
       $('#hinweise').html(hinw);
    
  }
  })

/////////////// DataTable-Einstellungen ////////////////////////

setTimeout(function() {
  if($('#output').html()=="&nbsp;"){
    $('#output').html("Fehler: Die Daten konnten nicht geladen werden.");
  }
  else{
  var table = $("#seitentabelle").DataTable( {
  columnDefs: [ {  orderable: true,
                   className: 'select-checkbox',
                   targets:   0    } ],
  order: [[ 1, 'asc' ]],
  autoWidth: true,
  paging: false,
  searching: true,
  info: false,

select: {
    info: true,
    style: 'multi+shift',
},
language: {   lengthMenu: "Zeige _MENU_ Zeilen",
                zeroRecords: "Keine Eintr\u00E4ge gefunden",
                info: "",
                infoEmpty: "",
                infoFiltered: " Eintr\u00E4ge ausgewaehlt)",
                paginate: { previous: "zurück",
                            next: "weiter"    },
                searchPlaceholder: 'Suche',
                search: ""  },
                
  buttons: [    {   text: 'Alle Eintr\u00E4ge',
                      action: function () {  table.rows().select(); } ,
                      className: "btn btn-extra-blau"  },
                {   text: 'Bisher bestellt',
                    action: function () {  
                      table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                      table.buttons( 1, null ).remove();
                      loadtable();},
                    className: "btn btn-extra-blau" 
                }  ,
                {   text: 'Keine Eintr\u00E4ge',
                      action: function () {  table.rows().deselect(); },   
                      className: "btn btn-extra-blau" 
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
    { extend: 'pdf', text: 'PDF',  className: "btn btn-primary primary-datatable" },  
    { extend: 'excel', text: 'Excel',   className: "btn btn-primary primary-datatable" },
    ]});

    new $.fn.dataTable.Buttons( table, {
        buttons: {
            dom: {
            collection: {
                tag: 'aside'
            }    }
        },
        buttons:[  
            'spacer',
          {   text: "Auswahl kostenpflichtig bestellen",
              action: function(){bestellen();},
              className: "btn btn-primary primary-datatable breit rund" 
          },
        ]});

  //vorausgewaehlteZellen haben ein &nbsp; statt nichts
  var indexes = table
      .rows()
      .indexes()
      .filter( function ( value, index ) {
          return '&nbsp;' === table.row(value).data()[0];
        } );
  table.rows( indexes ).select();

  table.buttons( 0, null ).containers().addClass('pufferLinks').appendTo( "#buttonleiste" );
  table.buttons( 1, null ).containers().addClass('btn-group flex-wrap pufferRechts').appendTo( "#buttonleiste" );
  table.buttons( 2, null ).containers().addClass('btn-group flex-wrap pufferRechts').appendTo( "#seitentabelle_filter" );

  
  $(':button').removeClass('dt-button');
  $(':button').removeClass('buttons-excel');
  $(':button').removeClass('buttons-html5 ');

  $("#seitentabelle tbody").on('click', 'tr', function () {  $(this).toggleClass('selected');   });
  
}},100); 
}

//Zustaendigen laden
function getZustaendigen(){
  let mail ="";
  let person = "";
  $.ajax({
    type: "GET",
    url: "../php/getZustaendigen.php",
    dataType : "json",
    success: function( data ) {
      $.each(data, function (index, value) {
        mail = value.Email;
        person = value.Admin;
        sperrdatum = value.Sperre;
      })
       $('#ansprechpartner').html("Ansprechpartner: "+person);     
       zustaendig = mail;
    }
  })
}

//Mail an Admin
function schreibMail(){
  window.location.href= "mailto:"+zustaendig;
}

//Modaler Dialog zur Bestellung /////////////////////////////////

function bestellen() {
    var table = $("#seitentabelle").DataTable();
    var data = table.rows( { selected: true } ).data();
    var anzahl = table.rows( { selected: true } ).count();
    var kosten = 0;
    for(let i=0; i<anzahl;i++){ 
         kosten=kosten+ parseInt(data[i][5]); 
    }
    $("#Kosten").text(kosten + " Euro");
    var nutzerid = $('#nutzerid').text();
    $( "#dialog-kaufen" ).dialog({
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
        text: "KOSTENPFLICHTIG BESTELLEN",
        icon: "ui-icon-check",
        class: "btn-warnend",
        click: function() {
          //Daten ermitteln
          var table = $("#seitentabelle").DataTable();
          var data = table.rows( { selected: true } ).data();
          var anzahl = table.rows( { selected: true } ).count();
          var zuBestellen = [];
          zuBestellen[0]=nutzerid;
          for(let i=0; i<anzahl;i++){ 
            zuBestellen[i+1]=data[i][1]; 
          };
          if(zuBestellen.length >= 1){
            var jsonString = JSON.stringify(zuBestellen);
          
            // Daten übermitteln an php/Server
          $.ajax({
              type: "POST",
              url: "../php/bestellungNutzer.php",
              data: {auswahl : jsonString}, 
              cache: false,
              dataType : "json",
              success: function( data ) {
                if(data == "abgelaufen"){
                  alert("Die Bestellfrist ist bereits abgelaufen.");
                }
              
                if(data == 0 || data == "abgelaufen"){
                table.buttons( 0, null ).remove();  // nötig, da sonst durch loadtable() doppelt vorhanden
                table.buttons( 1, null ).remove();
                loadtable();
                }
                else{
                  alert("Es ist ein Fehler aufgetreten.\nBitte beachten Sie, dass Sie bereits erhaltene Codes nicht mehr abbestellen k\u00F6nnen.\n Bitte wiederholen Sie Ihre Bestellung, wenn Sie m\u00F6gliche \u00C4nderungen vornehmen m\u00F6chten.");
                }
            
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

 
 