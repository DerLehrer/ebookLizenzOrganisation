$(document).ready(function() {

    $(document).keypress(function(event) {
       if (event.which == 13 ) {
            event.preventDefault();                                 //sonst reload nach Enter!
           if($("#loginName").val()!== undefined){
                  checkPW();
            }
            else if($("#registrieren").val()!== undefined){
                hashIt();
            }
            else if($("#Passwort").val()!== undefined){
                setNeu();
            }
        }
      });
      

    $('#togglePW').change(function () {
        var x = document.getElementById("Passwort");
        if (x.type === "password") {
          x.type = "text";
        } else {     x.type = "password";       }
    } )
       
    $('#togglePWE').change(function () {
        var x = document.getElementById("PasswortEingabe");
        if (x.type === "password") {
          x.type = "text";
        } else {              x.type = "password";          }
    } ) 

    $("#hinweisScheitern").hide();

    $("#hinweisScheitern").click(function(){
        $("#hinweisScheitern").text("Einen Moment bitte");
        $.ajax({
            type: "POST",
            url: "php/pwreset.php",
            data: { lN: $("#loginName").val()},
            dataType: "json",
            success: function(data){
            if(data==1){
                alert("Es wurde eine Mail zum Zur\u00FCcksetzen Ihres Passworts verschickt.");
                /* 
                ö = \u00F6  Ö  = \u00D6 
                ä = \u00E4  Ä  = \u00C4
                ü = \u00FC  Ü  = \u00DC
                ß = \u00DF  
                */    
                $("#hinweisScheitern").hide();
            }
            else if(data==-1){
                $("#hinweisScheitern").text("Das Verschicken der Zur\u00Fcksetzungs-Mail ist gescheitert.");
            }
            else if(data==0){
                $("#hinweisScheitern").text("Das Zur\u00Fcksetzen ist aktuell nicht m\u00F6glich.");
            }
            }
    })
      })
      $("#Impressum").click(function(){
        var ziel = "../impressum_i.html";
        if($("#seitenid").text()=="anmeldung"){
            ziel = "../impressum_b.html";
        }    
            window.location.assign(ziel);})

      $("#Datenschutz").click(function(){
        var ziel = "../datenschutzerklaerung_i.html";
        if($("#seitenid").text()=="anmeldung"){
            ziel = "../datenschutzerklaerung_b.html";
        }    
            window.location.assign(ziel);})
})


/////////////JQuery-Eingabequalität-Check///////////////

function checkPasswordStrength() {
	var number = /([0-9])/;
	var alphabets = /([a-zA-Z])/;
	var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<,-])/;
	var password = $('#Passwort').val().trim();
	if (password.length < 72 && password.length > 7 && password.match(number) && password.match(alphabets) && password.match(special_characters)) {
		$("#pwOK").text(1);
        $('#hinweisePasswort').html("&nbsp;");
    }
    else{
        $('#hinweisePasswort').html("mind. 8 Zeichen, 1 Zahl, 1 Buchstabe, 1 Sonderzeichen");
        $("#pwOK").text(0);
	} 
}

function checkBenutzername() {
    var benutzerlogin = $('#loginName').val().trim();
	if (benutzerlogin.length > 4 && benutzerlogin != "Benutzername") {
		$("#loginOK").text(1);
        $('#hinweiseBenutzername').html("&nbsp;");
    }
    else{
        $('#hinweiseBenutzername').html("mind. 5 Zeichen, nicht: 'Benutzername'");
        $("#loginOK").text(0);
	} 
}

///////////////Anmeldung inkl. PW-PRUEFEN/////////////////
 function checkPW(){
     $.ajax({
        type: "POST",
        url: "php/anmeldung.php",
        data: { lN: $("#loginName").val(),
                pW: $("#PasswortEingabe").val()},
        dataType: "json",
        success: function(data) {             
            if(data===true){
                if($("#loginName").val() =="Verwalter"){
                    window.location.href = "/php/buch.php";  
                }
                else{
                    window.location.href = "/php/bestellseite.php";  
                }
             }
             else {
                $.ajax({
                    type: "GET",
                    url: "/php/abmelden.php"
                })
                $("#hinweisScheitern").show();
                $("#spacer").hide();
             }
             }
             
            
        })
}

//////Registrieren //////

function hashIt(){
       checkBenutzername();
        checkPasswordStrength();
        if($("#pwOK").text()==1 && $("#loginOK").text()==1){
            if($("#annahme").prop("checked")!=true){
                $("#zustimmungstext").css('color', 'red');
            }
        else{
                  
        $.ajax({
        type: "POST",
        url: "php/registrieren.php",
        data: { aN: $("#loginName").val(),
                pW: $("#Passwort").val(),
                //pW: hashWert,
                eM: $("#anmelder").text(),
                otpw: $("#ePw").text()
        },
        dataType: "json",
        success: function(data) {
        if(data==9999) {
        $("#hinweiseBenutzername").text("Der Benutzername "+$("#loginName").val()+" ist bereits vergeben." );
         }
        else if(data==-1) {
            $("#hinweiseBenutzername").text("Es ist ein Fehler aufgetreten - die Registrierung ist gescheitert.");
             }
          else if(data==1){
             alert("Der Benutzername "+ $("#loginName").val() + " wurde angelegt");
        window.location.replace("Anmeldung.html");      // redirekt -> Back-Button funktioniert nicht!
    }}})
    }
        }
}    

    
///////////////PW zuruecksetzen /////////////////

function setNeu(){
    checkPasswordStrength();
    if($("#pwOK").text()==1){
    //alert($("#anmelder").text() +"->"+$("#Passwort").val()+"->"+ $("#ePw").text());
    $.ajax({
        type: "POST",
        url: "php/checkOneTimePW.php",
        data: { aN: $("#anmelder").text(),
                pW: $("#Passwort").val(),
                oTPW: $("#ePw").text()
                },
        success: function(data) {
        if(data==1) {
            alert("Das Passwort wurde aktualisiert." );
            window.location.replace("Anmeldung.html"); 
        }
        else if(data==-1) {
            $("#hinweiseBenutzername").text("Das Zeitlimit f\u00FCr die \u00C4nderung ist abgelaufen.");
             }
        else if(data==-2) {
                $("#hinweiseBenutzername").text("Es ist ein Fehler aufgetreten.");
                 }
        }
    })
}
}


 	