/* global $ municipalitiesUrl birthTownValue townValue */


$(function () {
    "use strict";

    if ($('#mobile').val() == '' || $('#mobile').val().length < 1) {
        $('#smsCode').val('');
    }

    if ($('#confirmSmsCode ul.error-sign-up').html()) {
        $('#confirmSmsCode').show();
    }
    
    if ($('#confirmSmsCode ul.errors').html()) {
        $('#confirmSmsCode').show();
    }


    if ($('#smsCode').val() != '') {
        $('#confirmSmsCode').show();
    }

    if (document.location.pathname === "/signup" || document.location.pathname === "/signup/mobile" ){
        if ($('#promocode').val().length > 0) {
            verifyPromo();
        }
    }

    $(document).on("click", "#buttonSendCode", function () {
        if ($('#mobile').val().length > 0) {
            var prefix = $('#dialCode').val();
            var mobile = $('#mobile').val();
            mobile = mobile.replace("+" + prefix, "");
            //mobile = mobile.replace("00" + prefix, "");
            if(mobile.substring(0, prefix.toString().length+2) == '00'+prefix){
                mobile = mobile.substring(prefix.toString().length+2, mobile.length);
            }
            mobile = mobile.replace(/\D/g, '');
            $('#mobile').val(mobile);
            $.ajax({
                type: "POST",
                url: "/signup-sms",
                data: {'email': $('#email').val(), 'mobile': $('#mobile').val(), 'dialCode': $('#dialCode').val()},
                beforeSend: function () {
                    //console.log("WAIT 1");
                    $('#buttonCode').hide();
                    $('#buttonCode').html("<div><i class='fa fa-spinner fa-pulse fa-2x fa-fw'></i>"+translate("sms_send")+"</div>");
                    $('#buttonCode').show();
                },
                success: function (data) {
                    $('#buttonCode').hide();
                    
                    switch (data.toString()) {
                        case "Wait message":
                            $('#buttonCode').html("<div>"+translate("sms_wait")+"</div>");

                            //$('#buttonCode').html("<div> <button id='buttonSendCode' type='button' >INVIA CODICE </button> </div>");
                            break;

                        case "OK":
                            //$('#buttonCode').html("<div><p style='color:green;'><i class='fa fa-check fa-2x' style='color:green'></i>Sms inviato</p></div>");
                            $('#buttonCode').html("<div><p style='color:green;'><i class='fa fa-check fa-2x' style='color:green'></i>"+translate("sms_sended")+"</p></div>");

                            $('#confirmSmsCode').fadeIn();
                            $("#js-sumbimit-editMobile").fadeIn();
                            break;

                        case "Errore invio sms":
                            //$('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>Errore nell'invio sms</p></div>");
                            $('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>"+translate("sms_error_send")+"</p></div>");
                            break;

                        case "Numero di telefono non corretto":
                            //$('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>Numero non corretto, attendere 60 secondi per riprovare</p></div>");
                            $('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>"+translate("sms_error_wrong_number")+"</p></div>");
                            break;

                        case "Found":
                            //$('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>Numero gi&agrave presente, attendere 60 secondi per riprovare</p></div>");
                            $('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>"+translate("sms_error_alredy_present")+"</p></div>");
                            break;

                    }

                    $('#buttonCode').show();

                    setTimeout(function () {
                        //$('#buttonCode').html("<div> <button id='buttonSendCode' type='button' >INVIA CODICE </button> </div>");
                        $('#buttonCode').html("<div> <button id='buttonSendCode' type='button' >"+translate("sms_button")+"</button> </div>");
                    }, 60000);
                },
                error: function () {
                    console.log("ERROR 1");
                }
            });
        } else {
            // if ($('#language').val() == "it") {
            //     alert("Inserire numero di telefono");
            // } else {
            //     alert("Insert phone number");
            // }

            alert(translate("sms_insert"));
        }
    });

    $("#birthCountry").change(function (event, params) {

        setLanguage(); //CSD947-170822-doublelanguagesignupforms//

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
            birthTownString.prop("disabled", true);
            birthTownString.val("EE");

            if (typeof params !== "undefined" && params.hasOwnProperty("birthTownValue")) {
                birthTownString.attr("value", params.birthTownValue.toUpperCase());
            } else {
                birthTownString.attr("value", "EE");
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
    if (typeof birthTownValue != "undefined") {
        $("#birthCountry").trigger("change", {
            birthTownValue: birthTownValue
        });
    }

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

    $("#country").change(function (event, params) {

        var province = $("#province"),
            provinceHidden = $("[type=hidden][name='user1[province]'], [type=hidden][name='customer[province]']"),
            townSelect = $("select#town"),
            townString = $("input#town"),
            zipCodeSelect = $("select#zipCode"),
            zipCodeString = $("input#zipCode");

        if ($(this).val() !== "it") {
            province.val("EE");
            province.prop("disabled", true);
            provinceHidden.val("EE");

            townSelect.hide();
            townString.show();
            townString.prop("disabled", false);

            zipCodeSelect.hide();
            zipCodeString.show();
            zipCodeString.prop("disabled", false);

            if (typeof params !== "undefined" && params.hasOwnProperty("townValue")) {
                townString.attr("value", params.townValue.toUpperCase());
            } else {
                townString.attr("value", "");
            }
        } else {
            if (province.val() === "EE") {
                province.val(0);
            }

            province.prop("disabled", false);
            provinceHidden.val("");

            townSelect.show();
            townString.hide();
            townString.prop("disabled", true);

            zipCodeSelect.show();
            zipCodeString.hide();
            zipCodeString.prop("disabled", true);
        }

        province.change();

    });

    if (typeof townValue != "undefined") {
        $("#country").trigger("change", {
            townValue: townValue
        });
    }

    $("#province").change(function (event, params) {

        var province = $(this).val(),
            promise;

        // clear present options
        $("#town option").remove();

        if (province !== 0 && province !== "0") {
            promise = $.get(municipalitiesUrl + "/" + province, function (data) {
                if (province === $("#province").val()) {
                    $.each(data, function (i, item) {
                        $("#town").append($("<option>", {
                            value: item.name,
                            text: item.name
                        }));
                    });

                    $("select#town").trigger("change");
                    return false;
                }
            });

            if (typeof params !== "undefined" && params.hasOwnProperty("townValue")) {
                promise.done(function () {
                    $("select#town").val(params.townValue.toUpperCase());
                });
            }

        } else {
            $("#town").append($("<option>"));
        }
    });

    $("#town").change(function (event, params) {

        if(typeof municipalitiesUrl === 'undefined') {  // fix problem inside signup
            return false;
        }

        var town = $(this).val(),
            promise;

        if(town == null) {
            town = townValue;
        }

        var province = $("select#province").val();

        // clear present options
        $("select#zipCode option").remove();

        if (province !== 0 && province !== "0") {
            promise = $.get(municipalitiesUrl + "/" + province, function (data) {
                if (province === $("#province").val()) {
                    $.each(data, function (i, item) {
                        if(town === item.name) {
                            if(item.zip_codes !== null) {
                                $.each(item.zip_codes, function (i, item) {
                                    $("#zipCode").append($("<option>", {
                                        value: item,
                                        text: item
                                    }));
                                });
                            } else {
                                $("#zipCode").append($("<option>", {
                                    value: "00000",
                                    text: "00000"
                                }));
                            }

                            return false;
                        }
                    });
                }
            });

            if (typeof params !== "undefined" && params.hasOwnProperty("zipCodeValue")) {
                promise.done(function () {
                    $("select#zipCode").val(params.zipCodeValue);
                });
            }
        } else {
            $("#zipCode").append($("<option>"));
        }


    });

    if (typeof birthTownValue != "undefined") {
        $("#birthProvince").trigger("change", {
            birthTownValue: birthTownValue
        });
    }

    // if (typeof townValue != "undefined") {
    //     $("#country").trigger("change", {
    //         townValue: townValue
    //     });
    // }


    if (typeof townValue != "undefined") {
        $("#province").trigger("change", {
            townValue: townValue
        });
    }

    if (typeof zipCodeValue != "undefined") {
        $("#town").trigger("change", {
            townValue: townValue,
            zipCodeValue: zipCodeValue
        });
    }

    //CSD947-170822-doublelanguagesignupforms//
    function setLanguage() {
        if ($('#birthCountry').val() !== "it") {

        } else {

            '<%Session["currLang"] = "' + 'it' + '"; %>';
        }

        $('#buttonSendCode').text(translate("send_code"));
        $('#promoCodeTitle').text(translate("promo_code"));
        $('#generalConditionTitle').text(translate("general_condition_title"));
        $('#generalConditionLink').text(translate("general_condition_link"));
        $('#generalCondition1Text').text(translate("general_condition1"));
        $('#generalCondition2Text').text(translate("general_condition2"));
        $('#regulationConditionLink').text(translate("regulation_condition"));
        $('#privacyConditionLink').text(translate("privacy_condition_link"));
        $('#privacyConditionText').text(translate("privacy_condition_title"));

        $('#cancelBtn').val(translate("cancel_button"));
        $('#nextBtn').val("cancel_button");
        $('#InfoPravacyTitle').text(translate("privacy_policy_title"));
        $('#InfoPravacyText').text(translate("privacy_policy_text"));

    }

    function verifyPromo()
    {
        $.ajax({
            type: "POST",
            url: "/signup-promocodeverify",

            data: {'promocode': $('#promocode').val()},
            beforeSend: function () {
                //console.log("WAIT 1");
                // $('#buttonVerifyPromo').hide();
                // if ($('#language').val() == "it") {
                //     $('#buttonVerifyPromo').html("<div ><i class='fa fa-spinner fa-pulse fa-1x fa-fw'></i></div>");
                // } else {
                //     $('#buttonVerifyPromo').html("<div'><i class='fa fa-spinner fa-pulse fa-1x fa-fw'></i>Verify</div>");
                // }

                $('#buttonVerifyPromo').html("<div'><i class='fa fa-spinner fa-pulse fa-1x fa-fw'></i>"+translate("verify_promocode_button")+"</div>");

                $('#buttonVerifyPromo').show();

            },
            success: function (data) {
                $('#buttonVerifyPromo').hide();
                $('#buttonVerifyPromo').html(translate("verify_promocode_button"));
                $('#buttonVerifyPromo').show();
                var info = JSON.parse(data);
                document.getElementById('errorepromo').style.display = "none";

                var message = 'Pacc. Benvenuto a ' + info.cost + " &euro; ";
                if(info.min !== null && info.min > 0)
                {
                    message = message + "e "+ info.min +  " min ";
                }

                if(info.disc !== null && info.disc > 0 )
                {
                    message = message + "con tariffa scontata al "+ info.disc +  "&#37;";
                }

                $('#promodiv').html("<div id='promodiv' class='block-field bw-f auto-margin  w-3-3 '>"+ message +"</div>");

                setTimeout(function () {
                    $('#buttonVerifyPromo').html("");
                }, 600000);
            },
            error: function () {
                $('#buttonVerifyPromo').html(translate("verify_promocode_button"));

                $('#promodiv').html("<div id='promodiv' ></div>");
                document.getElementById('errorepromo').style.display = "block";
                $('#errorepromo').html("<div id='errorepromo'><ul class='errors'><li>"+translate("verify_promocode_invalid")+"</li></ul></div>");


                console.log("ERROR PromoVerifyCode");
            }
        });
    }
    $(document).on("click", "#buttonVerifyPromo", function () { // momo send the promo code to controller then adjust min and eur in the html
        if ($('#promocode').val().length > 0) {

            verifyPromo();
        }
    });

});
