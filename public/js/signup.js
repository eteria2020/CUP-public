/* global $ municipalitiesUrl birthTownValue */

$(function () {
    "use strict";

if($('#mobile').val()=='' ||  $('#mobile').val().length<1){
  $('#smsCode').val('');
}


if($('#confermaCodiceSms ul.errors').html()){
   $('#confermaCodiceSms').show();  
}

$(document).on("click", "#bInvia", function(){
    //console.log("Prima ajax")    ;
$.ajax({
    //dataType:"text",
    type:"POST",
    url:"/signup-sms",
    data: {'email':$('#email').val(),'mobile':$('#dialCode').val()+$('#mobile').val()},
    beforeSend:function(){
         console.log("WAIT 1");
        //$('#buttonCode').disable();
        //$($this).hide(); 
        //$($this).html("<div><img src='http://blog.teamtreehouse.com/wp-content/uploads/2015/05/InternetSlowdown_Day.gif' height='30' width='30'/>Invio sms...</div>");
        //$($this).show();
        $('#buttonCode').hide(); 
            $('#buttonCode').html("<div><img src='http://blog.teamtreehouse.com/wp-content/uploads/2015/05/InternetSlowdown_Day.gif' height='30' width='30'/>Invio sms...</div>");
            $('#buttonCode').show();
    },
    success:function(data){
        
        switch(data.toString()){
            case "Attendere messaggio":
                alert("Messaggio già inviato,attendere");
                $('#buttonCode').html("<div> <button id='bInvia' type='button' >INVIA CODICE </button> </div>");
        break;
        
        default:
            $('#confermaCodiceSms').fadeIn();
            //$('#textCode').fadeIn();
            //$('#buttonControlCode').fadeIn();
            $('#buttonCode').html("<div> <button id='bInvia' type='button' >INVIA CODICE </button> </div>");
            //$('#buttonControlCode').html('<div> <button type="button" id="bConferma">CONFERMA CODICE</button> </div>');
            //$('#conferma').show();
        break;
        //$($this).delay( 5000 ).fadeOut();
        }
        console.log("SUCCESS 1 "+data.toString());    
    },
    error:function(){
        console.log("ERROR 1");
    }
    });
 }); 
 
 
$(document).on("click", "#bConferma", function(){
    //console.log("Prima ajax");
$.ajax({
    //dataType:"text",
    type:"POST",
    url:"/signup-verify-code",
    data: {'codiceUtenteSms':$('#smsCode').val()},
    
    beforeSend:function(){
         console.log("WAIT 2");
    },
    success:function(data){
        //console.log("Risultato: "+data.toString());
      
        switch(data.toString()){
            case "1":  $('#buttonControlCode').html("<div> <p style='color: green;'>NUMERO VERIFICATO</p> <img style='position:absolute; margin-left:190; margin-top:-50;' src='https://www.snapcard.io/img/wallet/check.svg' height='30' width='30' /> </div>");
                    break;
            case "2":$('#buttonControlCode').html("<div> <p style='color: red;'>CODICE ERRATO</p> <img style='margin-left:150; margin-top:-50;' src='http://www.drodd.com/images15/red-x21.jpg' height='30' width='30' /> </div>"); 
                    $('#buttonCode').html("<div> <button id='bInvia' type='button' >INVIA CODICE </button> </div>");
                    $('#confermaCodiceSms').show();
                    
                    break;
        
        }    
    },
    error:function(data){
        console.log("ERROR 2");
    }
});
 }); 

    $("#birthCountry").change(function (event, params) {
        var birthProvince = $("#birthProvince"),
            birthProvinceHidden = $("[type=hidden][name='user[birthProvince]'], [type=hidden][name='customer[birthProvince]']"),
            birthTownSelect = $("select#birthTown"),
            birthTownString = $("input#birthTown");

        if ($(this).val() !== "it") {
            birthProvince.val("EE");
            birthProvince.prop( "disabled", true );
            birthProvinceHidden.val("EE");
            birthTownSelect.hide();
            birthTownString.show();
            birthTownString.prop("disabled", false);

            if (typeof params !== "undefined" && params.hasOwnProperty("birthTownValue")) {
                birthTownString.attr("value", params.birthTownValue.toUpperCase());
            } else {
                birthTownString.attr("value", "");
            }
        } else {
            if (birthProvince.val() === "EE") {
                birthProvince.val(0);
            }

            birthProvince.prop( "disabled", false );
            birthProvinceHidden.val("");
            birthTownSelect.show();
            birthTownString.hide();
            birthTownString.prop("disabled", true);
        }

        birthProvince.change();
    });
    $("#birthCountry").trigger("change", {
        birthTownValue: birthTownValue
    });

    $("#birthProvince").change(function (event, params) {
        var province = $(this).val(),
            promise;

        // clear present options
        $("#birthTown option").remove();

        if (province !== 0 && province !== "0") {
            promise = $.get(municipalitiesUrl + "/" + province, function (data) {
                if (province === $("#birthProvince").val()) {
                    $.each(data, function (i, item) {
                        $("#birthTown").append($("<option>", {
                            value: item.name,
                            text: item.name
                        }));
                    });
                }
            });

            if (typeof params !== "undefined" && params.hasOwnProperty("birthTownValue")) {
                promise.done(function () {
                    $("select#birthTown").val(params.birthTownValue.toUpperCase());
                });
            }
        } else {
            $("#birthTown").append($("<option>"));
        }
    });
    $("#birthProvince").trigger("change", {
        birthTownValue: birthTownValue
    });
});
