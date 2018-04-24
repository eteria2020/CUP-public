/* global $ municipalitiesUrl birthTownValue */

const promoCodeTitle_IT = "Codice promo";
const promoCodeTitle_EN = "Promo code";

const generalConditionTitle_EN = "Terms and Conditions of Contract, Tariff Regulations and Privacy";
const generalConditionTitle_IT = "Termini e Condizioni Generali di Contratto, Regolamento Tariffario e Privacy";
const generalConditionTgeneralConditionTitle_ITitle_EN = "Main Service Contract";
const generalConditionLink_IT = "Termini e Condizioni Generali di Contratto";
const generalConditionLink_EN = "Terms and Conditions of Contract";
const generalCondition1_IT = "Ho letto, compreso e accettato i Termini e Condizioni Generali di Contratto e il Regolamento Tariffario del servizio di car sharing SHARE’NGO®, fornito da C.S. Group S.p.A. e dalle sue controllate: C.S. Firenze S.r.l., C.S. Milano S.r.l. e C.S. Roma S.r.l.";
const generalCondition1_EN = "I have read, understood and accepted the Terms and Conditions of Contract and the SHARE'NGO® car sharing service fee, provided by C.S. Group S.p.A. and its subsidiaries: C.S. Firenze S.r.l., C.S. Milano S.r.l. and C.S. Roma S.r.l.";
const generalCondition2_IT = "Dichiaro ai sensi e per gli effetti di cui agli artt. 1341 e 1342 c.c., avendo letto i presenti Termini e Condizioni Generali di Contratto di accettare espressamente e approvare specificatamente le condizioni di cui agli articoli: 3 (oggetto e parti del contratto), 4 (modifica unilaterale del Contratto), 5 (iscrizione e prenotazione online del Car Sharing SHARE’NGO®), 6 (tariffe e fatturazione), 7 (divieto di sublocazione e di cessione), 8 (esonero di responsabilità), 9 (permesso di guida), 10 (utilizzo dei veicoli. Clausola risolutiva espressa), 11 (sinistro o avaria del veicolo), 12 (furti e vandalismi), 13 (sanzioni in materia di circolazione stradale), 14 (assicurazioni), 16 (decorrenza, durata, rinnovo, sospensione, recesso, risoluzione del contratto), 17 (reclami), 18 (penali), 20 (foro competente).";
const generalCondition2_EN = "I declare to the senses and for the effects of cui to the artt. 1341 and 1342 of the Italian Civil Code, having read these General Terms and Conditions of Contract to expressly accept and approve the conditions specified in the articles: 3 (the object and parts of the contract), 4 (unilateral amendment of the Contract), 5 (registration and reservation (SHARE'NGO® Car Sharing), 6 (billing and billing), 7 (prohibition of sublicense and transfer), 8 (liability exemption), 9 (driving permit), 10 (use of vehicles). ), 11 (car accident or damage), 12 (theft and vandalism), 13 (road traffic penalties), 14 (insurance), 16 (termination, renewal, suspension, termination, termination of contract) 17 (complaints), 18 (criminal), 20 (competent court).";

const regulationConditionLink_IT = "Regolamento Tariffario";
const regulationConditionLink_EN = "Tariff Regulations";

const privacyConditionLink_IT = "Informativa Privacy";
const privacyConditionLink_EN = "Privacy disclaimer";
const privacyCondition_IT = "Ho letto, compreso e accettato l’Informativa Privacy per i Clienti SHARE’NGO® ed acconsento al trattamento dei miei dati personali secondo le modalità indicate.";
const privacyCondition_EN = "I have read, understood and accepted the Privacy Statement for SHARE'NGO® Customers and I agree to the processing of my personal details as indicated.";

const privacyPolicyTitle_IT = "Informativa sulla privacy";
const privacyPolicyTitle_EN = "Privacy Policy";
const privacyPolicyText_IT = "Al fine di migliorare il servizio ed essere aggiornato sulle offerte di SHARE’NGO® e dei partner di SHARE’NGO® riservate in via preferenziale e/o esclusiva ai clienti SHARE’NGO®, do il mio consenso a ricevere comunicazioni di SHARE’NGO® via email, SMS o posta, inclusi gli inviti a partecipare a indagini di mercato e sondaggi.";
const privacyPolicyText_EN = "In order to improve the service and be up-to-date on SHARE'NGO® and SHARE'NGO® Partner's preferred and / or exclusive deals to SHARE'NGO® customers, I agree to receive SHARE'NGO ® by email, SMS or mail, including invitations to participate in market surveys and surveys.";

const buttonSendCode_IT = "Invia codice";
const buttonSendCode_EN = "Send code";
const cancelBtn_IT = "Annulla";
const cancelBtn_EN = "Cancel";
const nextBtn_IT = "Avanti";
const nextBtn_EN = "Next";

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
            mobile = mobile.replace("00" + prefix, "");
            mobile = mobile.replace(/\D/g, '');
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
                            $("#js-sumbimit-editMobile").fadeIn();
                            break;

                        case "Errore invio sms":
                            $('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>Errore nell'invio sms</p></div>");
                            break;

                        case "Numero di telefono non corretto":
                            $('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>Numero non corretto</p></div>");
                            break;

                        case "Found":
                            $('#buttonCode').html("<div><p style='color:red;'><i class='fa fa-times fa-2x' style='color:red'></i>Numero gi&agrave presente</p></div>");
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
    if (typeof birthTownValue != "undefined") {
        $("#birthProvince").trigger("change", {
            birthTownValue: birthTownValue
        });
    }

    //CSD947-170822-doublelanguagesignupforms//
    function setLanguage() {
        if ($('#birthCountry').val() !== "it") {
            $('#promoCodeTitle').text(promoCodeTitle_EN);
            $('#generalConditionTitle').text(generalConditionTitle_EN);
            $('#generalConditionLink').text(generalConditionLink_EN);
            $('#generalCondition1Text').text(generalCondition1_EN);
            $('#generalCondition2Text').text(generalCondition2_EN);
            $('#regulationConditionLink').text(regulationConditionLink_EN);
            $('#privacyConditionLink').text(privacyConditionLink_EN);
            $('#privacyConditionText').text(privacyCondition_EN);
            $('#buttonSendCode').text(buttonSendCode_EN);
            $('#cancelBtn').val(cancelBtn_EN);
            $('#nextBtn').val(nextBtn_EN);
            $('#InfoPravacyTitle').text(privacyPolicyTitle_EN);
            $('#InfoPravacyText').text(privacyPolicyText_EN);
        } else {
            $('#promoCodeTitle').text(promoCodeTitle_IT);
            $('#generalConditionTitle').text(generalConditionTitle_IT);
            $('#generalConditionLink').text(generalConditionLink_IT);
            $('#generalCondition1Text').text(generalCondition1_IT);
            $('#generalCondition2Text').text(generalCondition2_IT);
            $('#regulationConditionLink').text(regulationConditionLink_IT);
            $('#privacyConditionLink').text(privacyConditionLink_IT);
            $('#privacyConditionText').text(privacyCondition_IT);
            $('#buttonSendCode').text(buttonSendCode_IT);
            $('#cancelBtn').val(cancelBtn_IT);
            $('#nextBtn').val(nextBtn_IT);
            $('#InfoPravacyTitle').text(privacyPolicyTitle_IT);
            $('#InfoPravacyText').text(privacyPolicyText_IT);
            '<%Session["currLang"] = "' + 'it' + '"; %>';
        }

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
                if ($('#language').val() == "it") {
                    $('#buttonVerifyPromo').html("<div ><i class='fa fa-spinner fa-pulse fa-1x fa-fw'></i></div>");
                } else {
                    $('#buttonVerifyPromo').html("<div'><i class='fa fa-spinner fa-pulse fa-1x fa-fw'></i>Verify</div>");
                }
                $('#buttonVerifyPromo').show();

            },
            success: function (data) {
                $('#buttonVerifyPromo').hide();
                $('#buttonVerifyPromo').html("Applica");
                $('#buttonVerifyPromo').show();
                var info = JSON.parse(data);
                document.getElementById('errorepromo').style.display = "none";
                var a = '';
                if(info.min != null && info.min > 0)
                {
                  a =  "con  "+ info.min +  " minuti bonus ";
                }
         
                 if(info.disc != null && info.disc > 0 )
                {
                   a = a +  "e hai la tariffa scontata del "+ info.disc +  "%";
                }
                
                $('#promodiv').html("<div id='promodiv' class='block-field bw-f auto-margin  w-3-3 '>Con questo codice promo l'iscrizione costa " + info.cost +" euro "+ a +"</div>");

                setTimeout(function () {
                    $('#buttonVerifyPromo').html("");
                }, 600000);
            },
            error: function () {
                $('#buttonVerifyPromo').html("Applica");

                $('#promodiv').html("<div id='promodiv' ></div>");
                document.getElementById('errorepromo').style.display = "block";
                $('#errorepromo').html("<div id='errorepromo'><ul class='errors'><li>Il codice inserito non è valido</li></ul></div>");


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
