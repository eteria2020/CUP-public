/* global $ municipalitiesUrl birthTownValue */

$(function () {
    "use strict";

if($('#mobile').val()=='' ||  $('#mobile').val().length<1){
  $('#smsCode').val('');
}


if($('#confirmSmsCode ul.errors').html()){
   $('#confirmSmsCode').show();
}

$(document).on("click", "#buttonSendCode", function(){
 if($('#mobile').val().length>0){
$.ajax({
    type:"POST",
    url:"/signup-sms",
    data: {'email':$('#email').val(),'mobile':$('#mobile').val(),'dialCode':$('#dialCode').val()},
    beforeSend:function(){
         console.log("WAIT 1");
        $('#buttonCode').hide(); 
            $('#buttonCode').html("<div><img src='http://www.enterthemothership.com/wp-content/uploads/2014/06/ajax-loader.gif' height='30' width='30'/>Invio sms...</div>");
            $('#buttonCode').show();
    },
    success:function(data){
        switch(data.toString()){
            case "Attendere messaggio":
                alert("Messaggio già inviato,attendere");
                $('#buttonCode').html("<div> <button id='buttonSendCode' type='button' >INVIA CODICE </button> </div>");
        break;
        
        default:
            $('#confirmSmsCode').fadeIn();
            $('#buttonCode').html("<div> <button id='buttonSendCode' type='button' >INVIA CODICE </button> </div>");
        break;
        }
        console.log("SUCCESS 1 "+data.toString());    
    },
    error:function(){
        console.log("ERROR 1");
    }
    });
 }else{
     alert("Inserire numero di telefono");
 }
 }); 
 
 
$(document).on("click", "#buttonConfirm", function(){
$.ajax({
    type:"POST",
    url:"/signup-verify-code",
    data: {'codiceUtenteSms':$('#smsCode').val()},
    beforeSend:function(){
         console.log("WAIT 2");
    },
    success:function(data){

        switch(data.toString()){
            case "1":  $('#buttonControlCode').html("<div> <p style='color: green;'>NUMERO VERIFICATO</p> <img style='position:absolute; margin-left:190; margin-top:-50;' src='https://www.snapcard.io/img/wallet/check.svg' height='30' width='30' /> </div>");
                    break;
            case "2":$('#buttonControlCode').html("<div> <p style='color: red;'>CODICE ERRATO</p> <img style='margin-left:150; margin-top:-50;' src='http://www.drodd.com/images15/red-x21.jpg' height='30' width='30' /> </div>"); 
                    $('#buttonCode').html("<div> <button id='buttonSendCode' type='button' >INVIA CODICE </button> </div>");
                    $('#confirmSmsCode').show();
                    
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
