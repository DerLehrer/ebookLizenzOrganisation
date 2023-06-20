$(document).ready(function() {

$("#buchautor").on("keypress",function(e){          // # ist name
            if (e.which==13) {                        // key 13 = enter
           // alert("ok");
           // verbinde();       nicht nötig, weil über require once in datenholen organisiert
           funktionsname();
           }    
    })
})

  var funktionsname = function(){

    $.ajax({
          // The URL for the request
          url: "dbquery.php",
 
    // The data to send (will be converted to a query string)
    data: {
        buchklasse: $("#buchklasse").val(),
        buchnummer: $("#buchnummer").val(),          //id dient der Auswahl des übergebenen Wertes
        buchtitel: $("#buchtitel").val(),  
        buchpreis: $("#buchpreis").val(),  
        buchautor: $("#buchautor").val(),  
    },
 
    // Whether this is a POST or GET request
    type: "POST",
 
    // The type of data we expect back
    dataType : "json",
    error: function(){alert("nope");},
    success: function( data ) {
       str = '<table width = 100%>';
        $.each(data, function (index, value) {
            str = str + '<tr><td>'+value.id+'</td><td>'+value.Klasse+'</td><td>'+value.Titel+'</td><td>'+value.Autoren+'</td><td>'+value.Preis+'</td>';
        })
         str = str + '</table>';
         $("#output").html(str);
}})}
