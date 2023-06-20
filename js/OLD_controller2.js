$(document).ready(function() {

   loadonstart();
 
    $(':file').on('change', function () {
        var file = this.files[0];
      
        if (file.size > 1024) {
          alert('max upload size is 1k');
        }
        var ext = file.name.split('.').pop();
        if(ext != "csv"){
          alert("Muss CSV-Datei sein");
      };
    } )
    
    
    $("#starten").click(function(){  
      
     //Datei muss als file_data an Formulardaten angehängt werden
      var file_data = $('#inputFile').prop('files')[0];   
      var form_data = new FormData();                  
      form_data.append('file', file_data);
    

      $.ajax({
        type: "POST",
        url: "php/dateiInDB.php",
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
        loadonstart();
       }  
      })
 })
})


loadonstart = function() {
  $.ajax({
    type: "GET",
    url: "php/getBuecher.php",
    dataType : "json",
    success: function( data ) {
      str = '<table id="example" class="display"  style="width:100%"><thead><tr><th></th><th>a</th><th>b</th><th>c</th><th>d</th><th>e</th></tr></thead><tbody>';
       $.each(data, function (index, value) {
           str = str + '<tr><td></td><td>'+value.id+'</td><td>'+value.Klasse+'</td><td>'+value.Titel+'</td><td>'+value.Autoren+'</td><td>'+value.Preis+'</td>';
       })
       str = str + '</tbody></table>';
       $('#output').html(str);
   } 
  })

setTimeout(function() {
  var table = $("#example").DataTable( {
  paging: false,
  searching: false,
  info: false,
  select: true,

   columnDefs: [ {
    orderable: false,
    className: 'select-checkbox',
    targets:   0
    } ],
    order: [[ 1, 'asc' ]],

    // Buttons
    dom: 'Bfrtip',                      //nutze stattdessen append um sie unabhängig platzieren zu können


  buttons: [
    {
      text: 'Select all',
      action: function () {
        table.rows().select();
      }
    },
    {
      text: 'Select none',
      action: function () {
        table.rows().deselect();
        
      }
    },
    {
      text: "auwerten",
      action: function (){

        var data = table.rows( { selected: true } ).data();
        var anzahl = table.rows( { selected: true } ).count();
        var string = "";
        for(let i=0; i<anzahl;i++){
            string = string + data[i][3] + "-";
        }
        alert(string);
      }  

}
  ],

  

  });
   
  $("#example tbody").on('click', 'tr', function () {
    $(this).toggleClass('selected');
});


},50);

}

 