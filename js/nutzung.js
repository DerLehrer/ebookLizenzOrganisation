$(document).ready(function() {
  $.ajax({
    type: "GET",
    url: "php/getInhalte.php",
    dataType: "json",
    success: function( data ) {
      str = '<table id="books" class="display"  style="width:100%"><thead><tr><th></th><th></th><th></th><th></th><th></th></thead><tbody>';
       $.each(data, function (index, value) {
           str = str + '<tr><td>'+value.Id+'</td><td>'+value.Klasse+'</td><td>'+value.Titel+'</td><td>'+value.Autoren+'</td><td>'+value.Preis+'</td></tr>';
       })
       str = str + '</tbody></table>';
       $('#output').html(str);
   } 
  })

  setTimeout(function() {
    var table = $("#books").DataTable( {
    
    paging: false,
    searching: false,
    info: false,
    select: true,
    sort: false,
    order: [[ 1, 'asc' ]],
       });
     
    $("#books tbody").on('click', 'tr', function () {
      $(this).toggleClass('selected');
  });

  
   },50);









})
