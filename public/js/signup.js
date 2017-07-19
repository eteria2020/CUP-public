/* global $ municipalitiesUrl birthTownValue */

$(function () {
    "use strict";

    if ($('#mobile').val() == '' || $('#mobile').val().length < 1) {
        $('#smsCode').val('');
    }


    if ($('#confirmSmsCode ul.errors').html()) {
        $('#confirmSmsCode').show();
    }


    if ($('#smsCode').val() != '') {
        $('#confirmSmsCode').show();
    }

//$('#fleet option:selected').change(function() {
    $('#fleet').change(function () {
        if ($(this).val() != "2") {
            $('.smsClass').hide();
        } else {
            $('.smsClass').show();
        }
    });


    $(document).on("click", "#buttonSendCode", function () {
        if ($('#mobile').val().length > 0) {
            var prefix = $('#dialCode').val();
            var mobile = $('#mobile').val();
            mobile = mobile.replace("+" + prefix, "");
            mobile = mobile.replace("00" + prefix, "");
            $('#mobile').val(mobile);
            $.ajax({
                type: "POST",
                url: "/signup-sms",
                data: {'email': $('#email').val(), 'mobile': $('#mobile').val(), 'dialCode': $('#dialCode').val()},
                beforeSend: function () {
                    //console.log("WAIT 1");
                    $('#buttonCode').hide();
                    if ($('#language').val() == "it") {
                        $('#buttonCode').html("<div><i class='fa fa-spinner fa-pulse fa-2x fa-fw'></i>Invio sms...</div>");
                    } else {
                        $('#buttonCode').html("<div><i class='fa fa-spinner fa-pulse fa-2x fa-fw'></i>Sending sms...</div>");
                    }
                    $('#buttonCode').show();
                },
                success: function (data) {
                    $('#buttonCode').hide();

                    switch (data.toString()) {
                        case "Wait message":
                            if ($('#language').val() == "it") {
                                //alert("Messaggio già inviato,attendere");
                                $('#buttonCode').html("<div>Sms già inviato, attendere 60 secondi</div>");
                            } else {
                                //alert("Message already sent,please wait");
                                $('#buttonCode').html("<div>Message already sent,please wait</div>");
                            }
                            //$('#buttonCode').html("<div> <button id='buttonSendCode' type='button' >INVIA CODICE </button> </div>");
                            break;

                        case "OK":
                            $('#buttonCode').html("<div><p style='color:green;'><i class='fa fa-check fa-2x' style='color:green'></i>Sms inviato</p></div>");
                            $('#confirmSmsCode').fadeIn();
                            break;

                        case "Errore invio sms":
                            $('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>Errore nell'invio sms</p></div>");
                            break;

                        case "Numero di telefono non corretto":
                            $('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>Numero non corretto</p></div>");
                            break;
                    }

                    $('#buttonCode').show();

                    setTimeout(function () {
                        $('#buttonCode').html("<div> <button id='buttonSendCode' type='button' >INVIA CODICE </button> </div>");
                    }, 60000);
                },
                error: function () {
                    console.log("ERROR 1");
                }
            });
        } else {
            if ($('#language').val() == "it") {
                alert("Inserire numero di telefono");
            } else {
                alert("Insert phone number");
            }
        }
    });

    $("#birthCountry").change(function (event, params) {
        var birthProvince = $("#birthProvince"),
                birthProvinceHidden = $("[type=hidden][name='user[birthProvince]'], [type=hidden][name='customer[birthProvince]']"),
                birthTownSelect = $("select#birthTown"),
                birthTownString = $("input#birthTown");

        if ($(this).val() !== "it") {
            birthProvince.val("EE");
            birthProvince.prop("disabled", true);
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

            birthProvince.prop("disabled", false);
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



    $.ajax({
        type: "POST",
        url: "/fleet-id-sms-verification",
        success: function (data) {
            if ($.inArray($('#fleet').val(), data) != -1) {
                $('.smsClass').show();
            } else {
                $('.smsClass').hide()
            }
        },
        error: function () {
            console.log("ERROR fleet");
        }
    });

    $('#registration-form').submit(function (event) {
        if ($('#smsCode').val() == '' && $('#fleet').val() == "2") {
            alert('Inserire il codice di verifica sms ');
            return false;
        }
    });


});
