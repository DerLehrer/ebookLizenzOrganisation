$(document).ready(function() {
  var zustaendig =""
  var sperrdatum = null;
  getZustaendigen();
  makeDT();
  loadtable();

    /*        ö = \u00F6  Ö  = \u00D6 
              ä = \u00E4  Ä  = \u00C4
              ü = \u00FC  Ü  = \u00DC
              ß = \u00DF  
              */
  $("#Impressum").click(function(){window.location.assign("../impressum_b.html");})
  $("#Datenschutz").click(function(){window.location.assign("../datenschutzerklaerung_b.html");})   

  //Suchfunktion (erlaubt Platzierung des Suchfelds außerhalb des datatables)
  $("#suche").on('keyup change search', function() {
      $("#seitentabelle").DataTable().search($('#suche').val()).draw();       //inputfeld überträgt in dt-Suchfeld    
  });

})

/////////// DataTable aus Datenabruf erstellen ///////////////

function loadtable(){
  $.ajax({
  type: "GET",
  url: "../php/getMeineBestellungen.php",
  dataType : "json",
  success: function( data ) {
    let tab = $("#seitentabelle").DataTable();
    tab.clear();
    let kosten = 0.00;
     $.each(data, function (index, value) {
      let code = "-";
      if(value.Bestellt == '&nbsp;'){kosten = 1*kosten + 1*value.Preis}
      if(value.Code != null){code = value.Code;}
      tab.row.add(["",value.Bestellt,value.Buch,value.Stufe, value.Fach, value.Verlag, value.Preis, code]);
     })
     tab.draw();
     tab.columns.adjust();

    //vorausgewaehlteZellen haben ein &nbsp; statt nichts
    var indexes = tab
      .rows()
      .indexes()
      .filter( function ( value, index ) {
        return '&nbsp;' === tab.row(value).data()[1];
      } );
    tab.rows( indexes ).select();

     let hinw = "";
     if(sperrdatum != null && sperrdatum != "0000-00-00"){
      let jahr = sperrdatum.substring(0,4);
      let monat = sperrdatum.substring(5,7);
      let tag = sperrdatum.substring(8,10);
      hinw += "<div style='color:rgb(220, 53, 69); font-size:1.1em ; font-weight:bold'>Bestellungen m\u00FCssen VOR dem "+tag+"."+monat+"."+jahr+" erfolgen<br></div>";
     }
     hinw += "<div style='color:#007bff; font-size:1.1em ; font-weight:normal'>Sie haben ebooks im Wert von "+kosten+" Euro bestellt.</div><p>";

     $('#hinweise').html(hinw);
}
})

} 
/////////////// DataTable-Einstellungen zur Initialisierung ////////////////////////
function makeDT(){
var table = $("#seitentabelle").DataTable( {
  dom: 'lrtip',                       // ohne f um das Originalsuchfeld/-filterfeld zu verstecken
  columnDefs: [ { orderable: true,
                  className: 'select-checkbox',
                  targets:   1    
                 },
                 {
                 //extraSpalte für responsive-extraRow-Control
                  className: 'dtr-control',
                  orderable: false,
                  targets: 0
                 }   
               ],
  fixedHeader: true,
  order: [[ 3, 'asc' ]],    //Standardsortierung nach Jgst.
  autoWidth: true,
  paging: false,
  searching: true,
  info: false,
  select: {
            info: true,
            style: 'multi+shift',
             selector: 'tr td:not(:first-child)'
          },
  language: {   lengthMenu: "Zeige _MENU_ Zeilen",
                zeroRecords: "Keine Eintr\u00E4ge gefunden",
                info: "",
                infoEmpty: "",
                infoFiltered: " Eintr\u00E4ge ausgewaehlt)",
                paginate: { previous: "zurück",
                          next: "weiter"    },
            },
  responsive: {
    details: {
      type: 'column',
         //extrazeile zeigt ausgeblendete Zellen, außer die "Auswahl"-Zelle (Index 1)
          renderer: function (api, rowIdx, columns) {
            let data = columns.map((col, i) => {
                return col.hidden && col.columnIndex != 1
                    ? '<tr data-dt-row="' +
                            col.rowIndex +
                            '"data-dt-column="' +
                            col.columnIndex +
                            '">' +
                            '<td>' +
                            col.title +
                            ':' +
                            '</td> ' +
                            '<td>' +
                            col.data +
                            '</td>' +
                            '</tr>'
                    : '';
            }).join('');
            let table = document.createElement('table');
            table.innerHTML = data;
            return data ? table : false;
        }
     }
  },
  buttons: [  { text: 'Hilfe',  
                 className: "btn btn-danger hilfe", //btn-extra-blau primary-hilfe
                 action: function () {  window.open('../anleitungen/Hilfe.pdf'); }   },
              {   text: 'Bisher bestellt',
                  action: function () {  
                    loadtable();},
                  className: "btn btn-extra-blau" 
              }  ,
              {   text: 'Wahl leeren',
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
  { extend: 'excel', text: 'Excel', className: "btn btn-primary primary-datatable" },
  ]});

  new $.fn.dataTable.Buttons( table, {
      buttons: {
          dom: {
          collection: {
              tag: 'aside'
          }    }
      },
      buttons:[  
        {   text: "Auswahl kostenpflichtig bestellen",
            action: function(){bestellen();},
            className: "btn btn-primary primary-datatable breit rund" 
        },
      ]});

table.buttons( 0, null ).containers().appendTo( "#buttonleiste" );
table.buttons( 1, null ).containers().addClass('btn-group flex-wrap ').appendTo( "#buttonleiste2" );
table.buttons( 2, null ).containers().addClass('btn-group flex-wrap ').appendTo( "#buttonleiste4" );


$(':button').removeClass('dt-button');
$(':button').removeClass('buttons-excel');
$(':button').removeClass('buttons-html5 ');


}; 


//Zustaendigen laden
function getZustaendigen(){

let mail ="";
let person = "";
$.ajax({
  type: "GET",
  url: "../php/getZustaendigen.php",
  dataType : "json",
  cache: false,
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
  var kosten = 0.00;
  for(let i=0; i<anzahl;i++){ 
       kosten = (kosten*100 + parseInt(data[i][6]*100))/100; 
  }
  $("#Kosten").text(kosten.toFixed(2) + " Euro");
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
        if($('#widerrufsrechtEinverstanden').prop('checked')==false) {
          $("#widerrufsverzichtserklaerung").html("<div style='color: red'><b>Hiermit erteile ich meine Zustimmung zur unverz&uumlglichen Ausf&uumlhrung des Vertrags und nehme zur Kenntnis, dass ich mein Recht auf Widerruf des Vertrags verliere, sobald der digitale Inhalt zur Verf&uumlgung gestellt wurde.</b></div>");
      } else {
        $('#widerrufsrechtEinverstanden').prop( "checked", false );   //zurücksetzen!
        $("#widerrufsverzichtserklaerung").html("Hiermit erteile ich meine Zustimmung zur unverz&uumlglichen Ausf&uumlhrung des Vertrags und nehme zur Kenntnis, dass ich mein Recht auf Widerruf des Vertrags verliere, sobald der digitale Inhalt zur Verf&uumlgung gestellt wurde.");
     
        //Daten ermitteln
        var table = $("#seitentabelle").DataTable();
        var data = table.rows( { selected: true } ).data();
        var anzahl = table.rows( { selected: true } ).count();
        var zuBestellen = [];
        zuBestellen[0]=nutzerid;
        for(let i=0; i<anzahl;i++){ 
          zuBestellen[i+1]=data[i][2]; 
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

              loadtable();
              }
              else{
                alert("Es ist ein Fehler aufgetreten.\nBitte beachten Sie, dass Sie bereits erhaltene Codes nicht mehr abbestellen k\u00F6nnen.\nBitte wiederholen Sie Ihre Bestellung, wenn Sie m\u00F6gliche \u00C4nderungen vornehmen m\u00F6chten.");
                loadtable();
              }
          
          }
            })
            $( this ).dialog( "close" );  
            }
            else{
        }
      }
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
  },
    );
} ; 




		