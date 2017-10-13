/* global $ municipalitiesUrl birthTownValue */

const promoCodeTitle_IT = "Codice promo";
const promoCodeTitle_EN = "Promo code";

const generalConditionTitle_EN = "Main Service Contract";
const generalConditionTitle_IT = "Condizioni Generali di Contratto";
const generalConditionTgeneralConditionTitle_ITitle_EN = "Main Service Contract";
const generalConditionLink_IT = "Scarica il documento e verifica le condizioni";
const generalConditionLink_EN = "Download the document and check the Main Service Contract";
const generalCondition1_IT = "ho letto e accetto le condizioni generali di contratto del servizio di car sharing fornito da C.S. Group S.p.A. e le sue controllate";
const generalCondition1_EN = "I HAVE READ AND AGREE TO THE MAIN SERVICE CONTRACT  OF THE CAR SHARING SERVICE PROVIDED BY C.S. GROUP S.P.A. AND ITS SUBSIDIARIES";
const generalCondition2_IT = "dichiaro ai sensi e per gli effetti di cui all’art. 1341 c.c. e segg., di accettare espressamente ed approvare specificatamente le condizioni di cui agli articoli: 1 (premesse), 2 (definizioni), 3 (oggetto e parti del contratto), 4 (divieto di sostituzione), 5 (modifica unilaterale del Contratto e del Regolamento del servizio di car sharing), 6 (iscrizione e prenotazione online del Car Sharing SHARE’NGO), 7 (garanzia economica del noleggio), 8 (tariffe), 9 (obblighi, fatturazione e pagamenti), 10 (divieto di sublocazione e di cessione), 11 (esonero di responsabilità), 12 (permesso di guida), 13 (utilizzo dei veicoli. Clausola risolutiva espressa), 14 (sinistro o avaria del veicolo), 15 (furti e vandalismi), 16 (sanzioni in materia di circolazione stradale), 17 (responsabilità del Cliente), 18 (assicurazioni – oneri a carico del Cliente), 19 (limiti di responsabilità), 20 (dati personali), 21 (decorrenza, durata, rinnovo, sospensione, recesso, risoluzione del contratto), 22 (reclami), 23 (diritto di recesso del Cliente), 24 (penali), 25 (comunicazioni) 26 (foro competente), 27 (varie).";
const generalCondition2_EN = "ACCORDINGLY TO ARTICLES 1341 AND 1342 C.C. OF ITALIAN LAW (CIVIL CODE),  I HEREBY DECLARE TO SPECIFICALLY ACCEPT  ARTICLES: 1 (PREMISE), 2 (DEFINITIONS), 3 (OBJECT AND CONTRACT PARTS), 4 (EXCLUSIVITY OF UTILIZATION), 5 (UNILATERAL MODIFICATION OF THE CONTRACT AND SERVICE TERMS AND CONDITIONS), 6 (ONLINE REGISTRATION AND BOOKING), 7 (RENTAL FEE GUARANTEE), 8 (FEES), 9 (OBLIGATIONS, INVOICING AND PAYMENTS), 10 (PROHIBITION OF SUB LOCATION), 11 ( RESPONSIBILITY), 12 (DRIVER’S LICENSE), 13 (UTILIZATION OF VEHICLES) 14 (DAMAGES AND BREAKDOWNS), 15 (THEFTS AND VANDALISM), 16 (LIABILITIES FOR BREACHING  ROAD CIRCULATION LAWS AND REGULATIONS), 17 (CUSTOMER'S RESPONSIBILITY), 18 (INSURANCE , EXEMPTIONS AND DEDUCTIONS), 19 (RESPONSIBILITY LIMITS), 20 (PERSONAL DATA), 21 (LENGTH, DURATION, RENEWAL, SUSPENSION, WITHDRAWAL and RESOLUTION OF THE CONTRACT), 22 (CLAIMS), 23 (CUSTOMER'S WITHDRAWAL), 24 (PENALTIES), 25 (COMMUNICATIONS) 26 (RULING LAW), 27 (MISCELLANEOUS).";

const regulationConditionTitle_IT = "Regolamento";
const regulationConditionTitle_EN = "Service Terms and Conditions";
const regulationConditionLink_IT = "Scarica il documento e verifica le condizioni";
const regulationConditionLink_EN = "Download the document and check the Service Terms and conditions";
const regulationCondition1_IT = "ho letto e accetto il Regolamento di servizio di car sharing Share'nGo fornito da C.S. Group S.p.A. e le sue controllate";
const regulationCondition1_EN = "I HAVE READ AND AGREE TO THE GENERAL TERMS AND CONDITIONS OF THE CAR SHARING SERVICE PROVIDED BY C.S. GROUP S.P.A. AND YOUR SUBSIDIARIES.";
const regulationCondition2_IT = "dichiaro ai fini di cui agli articoli 1341 e 1342 c.c. e ad ogni altro fine di legge, di accettare integralmente ed approvare specificamente le seguenti clausole del presente regolamento di cui agli articoli: 1 (adesione al servizio), 2 (iscrizione), 3 (prenotazione del veicolo), 4 (inizio del noleggio), 5 (avvio e verifiche preliminari del veicolo), 6 (batterie ed autonomia), 7 (utilizzo dei veicoli), 8 (restituzione del veicolo, parcheggio), 9 (pulizia del veicolo e ritrovamento oggetti), 10 (tariffe), 11 (profili tariffari), 12 (fatturazione), 13 (danni e malfunzionamento del veicolo C.S.), 14 (sinistro o avaria del veicolo), 15 (incendio, furto, rapina, atti vandalici), 16 (varie).";
const regulationCondition2_EN = "ACCORDINGLY TO ARTICLES 1341 AND 1342 C.C. OF ITALIAN LAW (CIVIL CODE),  I HEREBY DECLARE TO SPECIFICALLY ACCEPT THE FOLLOWING CLAUSES OF THIS REGULATION : 1 (ADMISSION TO SERVICE), 2 (REGISTRATION), 3 (VEHICLE BOOKING), 4 (START OF RENTAL) , 5 (VEHICLE PRELIMINARY CHEKS AND START), 6 (BATTERY AND RANGE), 7 (VEHICLE UTILIZATION), 8 (VEHICLE RETURN, PARKING), 9 (CLEANING OF THE VEHICLE AND LOST&FOUND), 10 (RATES), 11 (PERSONAL RATES), 12 (INVOICING), 13 (DAMAGES AND VEHICLE MANFUNCTIONING), 14 (CAR CRASH AND BREAKDOWNS), 15 ( FIRE, THEFT, VANDALISM), 16 (MISCELLANEA).";

const privacyConditionLink_IT = "Scarica il documento e verifica le condizioni";
const privacyConditionLink_EN = "Download the document and check our Privacy Policy";
const privacyCondition_IT = "ho letto l’Informativa Privacy ed acconsento al trattamento dei miei dati personali secondo le modalità indicate";
const privacyCondition_EN = "HEREBY CONFIRM THAT I HAVE RECEIVED, REVIEWED AND UNDERSTAND THE SHARE’NGO PRIVACY POLICY. I ACKNOWLEDGE AND ACCEPT THIS  POLICY AS IT RELATES TO ANY INFORMATION I PROVIDE AS PART OF MY APPLICATION, SUBSEQUENT SHARE’NGO MEMBERSHIP AND CAR UTILIZATION.";

const privacyPolicyTitle_IT = "Informativa sulla privacy";
const privacyPolicyTitle_EN = "Privacy Policy";
const privacyPolicyText_IT = "HO LETTO E COMPRESO L’INFORMATIVA SULLA PRIVACY E DO IL MIO CONSENSO PER IL TRATTAMENTO DEI MIEI DATI PERSONALI PER FINALITÀ DI MARKETING TRAMITE E EMAIL, TELEFONO, SMS, MMS E POSTA TRADIZIONALE CON RIFERIMENTO AI PRODOTTI, SERVIZI E PROMOZIONI SHARE’NGO, INCLUSE LE RICERCHE DI MERCATO. ";
const privacyPolicyText_EN = "I BOTH AND INCLUDE THE INFORMATION ON PRIVACY AND I DO MY CONSENT FOR THE PERSONAL DATA PROCESSING FOR MARKETING PURPOSES THROUGH EMAIL, TELEPHONE, SMS, MMS AND TRADITIONAL POSITION WITH REFERENCE TO SHARE'NGO PRODUCTS, SERVICES AND PROMOTIONS, INCLUDING MARKET RESEARCH.";

const neswletterTitle_IT = "Newsletter";
const neswletterTitle_EN = "Newsletter";
const neswletterText_IT = "DESIDERO ISCRIVERMI ALLA NEWSLETTER DI SHARE'NGO. ";
const neswletterText_EN = "I WANT TO JOIN THE SHARE'NGO NEWSLETTER.";

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


    if ($('#confirmSmsCode ul.errors').html()) {
        $('#confirmSmsCode').show();
    }


    if ($('#smsCode').val() != '') {
        $('#confirmSmsCode').show();
    }

    if ($('#name').val().length > 0) {

        verifyPromo();
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

    //CSD947-170822-doublelanguagesignupforms//
    function setLanguage() {
        if ($('#birthCountry').val() !== "it") {
            $('#promoCodeTitle').text(promoCodeTitle_EN);
            $('#generalConditionTitle').text(generalConditionTitle_EN);
            $('#generalConditionLink').text(generalConditionLink_EN);
            $('#generalCondition1Text').text(generalCondition1_EN);
            $('#generalCondition2Text').text(generalCondition2_EN);
            $('#regulationConditionTitle').text(regulationConditionTitle_EN);
            $('#regulationConditionLink').text(regulationConditionLink_EN);
            $('#regulationCondition1Text').text(regulationCondition1_EN);
            $('#regulationCondition2Text').text(regulationCondition2_EN);
            $('#privacyConditionLink').text(privacyConditionLink_EN);
            $('#privacyConditionText').text(privacyCondition_EN);
            $('#buttonSendCode').text(buttonSendCode_EN);
            $('#cancelBtn').val(cancelBtn_EN);
            $('#nextBtn').val(nextBtn_EN);
            $('#newsletterTitle').text(neswletterTitle_EN);
            $('#newsletterText').text(neswletterText_EN);
            $('#InfoPravacyTitle').text(privacyPolicyTitle_EN);
            $('#InfoPravacyText').text(privacyPolicyText_EN);
        } else {
            $('#promoCodeTitle').text(promoCodeTitle_IT);
            $('#generalConditionTitle').text(generalConditionTitle_IT);
            $('#generalConditionLink').text(generalConditionLink_IT);
            $('#generalCondition1Text').text(generalCondition1_IT);
            $('#generalCondition2Text').text(generalCondition2_IT);
            $('#regulationConditionTitle').text(regulationConditionTitle_IT);
            $('#regulationConditionLink').text(regulationConditionLink_IT);
            $('#regulationCondition1Text').text(regulationCondition1_IT);
            $('#regulationCondition2Text').text(regulationCondition2_IT);
            $('#privacyConditionLink').text(privacyConditionLink_IT);
            $('#privacyConditionText').text(privacyCondition_IT);
            $('#buttonSendCode').text(buttonSendCode_IT);
            $('#cancelBtn').val(cancelBtn_IT);
            $('#nextBtn').val(nextBtn_IT);
            $('#newsletterTitle').text(neswletterTitle_IT);
            $('#InfoPravacyTitle').text(privacyPolicyTitle_IT);
            $('#InfoPravacyText').text(privacyPolicyText_IT);
            $('#newsletterText').text(neswletterText_IT);
            '<%Session["currLang"] = "' + 'it' + '"; %>';
        }

    }

    function verifyPromo()
    {
        $.ajax({
            type: "POST",
            url: "/signup-promocodeverify",

            data: {'promocode': $('#name').val()},
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
        if ($('#name').val().length > 0) {

            verifyPromo();
        }
    });

});
