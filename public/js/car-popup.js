/* Global variables */

// hold the km value in this var
var oldKm;
// text to enable spinner
var spinner = '<i class="fa fa-circle-o-notch fa-spin"></i>';
// used to disable modifications when popup is closed
var isOpen = false;
// last clicked car's position
var carPos;
// last clicked car's battery
var carBattery;
// user's reservation id
var reservationId;
// last clicked car
var car;
// last clicked marker
var marker;

// get html elements
var mainContainer = document.getElementById('car-popup');
var btnClose = document.getElementById('btn-close');
var btnCoverage = document.getElementById('coverage');
// elements modified for second popup
var leftColumn = document.getElementById('left-column');
var rightColumn = document.getElementById('right-column');
var btnReserve = document.getElementById('btn-reserve');
var step2Buttons = document.getElementById('step2-buttons');
var circleIcon = document.getElementById('circle-icon');
var blockRightBottomTitle = document.getElementById('block-right-bottom-title');
var blockRightBottomText = document.getElementById('block-right-bottom-text');
var btnBack = document.getElementById('btn-back');
var btnConfirm = document.getElementById('btn-confirm');
var isReservedDiv = document.getElementById('reserve-text');
// elements modified on marker click event
var plateDiv = document.getElementById('licence-plate');
var intCleanDiv = document.getElementById('int_cleanliness');
var extCleanDiv = document.getElementById('ext_cleanliness');
var locationDiv = document.getElementById('location');
// elements modified in last popup
var blockRightTopDiv = document.getElementById('block-right-top');
var btnDone = document.getElementById('btn-done');



/* Add the event listeners */

btnClose.addEventListener('click', function(event)
{
    close();
});
btnBack.addEventListener('click', function(event)
{
    reset();
});
btnConfirm.addEventListener('click', function(event)
{
    confirm();
});
btnDone.addEventListener('click', function(event)
{
    close();
});
btnCoverage.addEventListener('click', function(event)
{
    verifyCoverage();
});
btnReserve.addEventListener('click', function(event)
{
    startAction();
});



/* Handle the btnReserve different actions */

// stores btnReserve click action's state
var actionNumber = 0; // states are: 0=inactive, 1=nextStep, 2=removeReservation

// call this function to set the action on btnReserve click event
function setAction(number, resId)
{
    actionNumber = number;
    reservationId = resId;
}

// this is called when btnReserve is clicked
function startAction()
{
    if (actionNumber == 1) {
        nextStep();
    } else if (actionNumber == 2) {
        removeReservation();
    }

    //if actionNumber == 0 (or anything else) do nothing
}

// next step to reserve car
function nextStep()
{
    leftColumn.style.display = "none";
    rightColumn.style.width = "100%";
    btnReserve.style.display = "none";
    step2Buttons.style.display = "inline";
    btnConfirm.innerHTML = textButtonConfirm;
    circleIcon.style.display = "none";
    setRightBottomBlockTitle(titleRemember, 2);
    oldKm = blockRightBottomText.innerHTML;
    blockRightBottomText.innerHTML = textRemember;
    btnCoverage.style.display = "none";
}

// avoid multiple function calls
var isRemovingReservation = false;

// remove a car reservation
function removeReservation()
{
    if (!isRemovingReservation) {

        isRemovingReservation = true;
        isReservedDiv.innerHTML = spinner;

        $.ajax({
            url: reservationsUrl + '\\' + reservationId,
            type: 'DELETE',
            success: function(jsonData) {
                // do not change layout if popup is closed
                if (isOpen) {
                    nextStep();
                    if (typeof jsonData.reason !== 'undefined' && jsonData.reason !== null && jsonData.reason == 'OK') {
                        completed(textReservationRemoved);
                        car['isReservedByCurrentUser'] = false;
                        marker.setIcon(carMarkerPath);
                    } else {
                        completed(textReservationRemovedNot);
                    }
                }
                isRemovingReservation = false;
            }
        });

    }
}



/* Create the other actions */


// show popup
function showPopup(carParam, markerParam)
{
    car = carParam;
    marker = markerParam;
    mainContainer.style.display = "inline";
    isOpen = true;
}

// reset the popup to the first screen
function reset()
{
    leftColumn.style.display = "block";
    rightColumn.style.width = "";
    btnReserve.style.display = "inline";
    step2Buttons.style.display = "none";
    circleIcon.style.display = "block";
    setRightBottomBlockTitle(titleMilage, 1);
    blockRightBottomText.innerHTML = oldKm;
    btnCoverage.style.display = "inline";
}

// close the popup and reset some data
function close()
{
    mainContainer.style.display = "none";
    isReservedDiv.innerHTML = spinner;
    setAction(0);
    blockRightTopDiv.style.display = "block";
    btnDone.style.display = "none";
    setLocationText(spinner);
    reset();
    isOpen = false;
}

// avoid multiple function calls
var isConfirmingReservation = false;

// confirm reservation
function confirm()
{
    if (!isConfirmingReservation) {
        isConfirmingReservation = true;

        btnConfirm.innerHTML = spinner;

        var plate = document.getElementById('licence-plate').innerHTML;

        $.post(reservationsUrl, 'plate=' + plate, function (jsonData)
        {
            // do not change layout if popup is closed
            if (isOpen) {
                if (typeof jsonData.reason !== 'undefined' && jsonData.reason !== null) {
                    if (jsonData.status == 200) {
                        car['isReservedByCurrentUser'] = true;
                        marker.setIcon(carMarkerPathReserved);
                        completed(jsonData.reason);
                    } else {
                        completed(jsonData.reason);
                    }
                } else {
                    completed(textReservationCompletedNot);
                }
            }
            isConfirmingReservation = false;

        });

    }

}

// change popup to last step and display message
function completed(text)
{
    blockRightTopDiv.style.display = "none";
    step2Buttons.style.display = "none";
    btnDone.style.display = "table";
    setRightBottomBlockTitle(text, 3);
    blockRightBottomText.innerHTML = '';
}

// draw a circle on the map with the coverage
function verifyCoverage()
{
    drawCoverage(carPos, carBattery);
    close();
}



/* Setters */

function setReserveText(text, setIcon)
{
    if (isOpen) {
        isReservedDiv.innerHTML = text + (setIcon ? ' <i class="fa fa-angle-right"></i>' : '');
    }
}

function setReserveButton(visible)
{
    isReservedDiv.style.display = (visible) ? "block" : "none";
}

function setRightBottomBlockTitle(text, stepNumber)
{
    if (stepNumber == 1) {
        text = '<i id="circle-icon" class="fa fa-sun-o"></i> ' + text;
    } else if (stepNumber == 2) {
        text = '<i id="circle-icon" class="fa fa-info-circle"></i> ' + text;
    }
    blockRightBottomTitle.innerHTML = text;
}

function setPlateText(text)
{
    plateDiv.innerHTML = text;
}

function setLocationText(text)
{
    locationDiv.innerHTML = text;
}

function setBatteryText(battery)
{
    mileage = Math.round(0.6 * battery);
    blockRightBottomText.innerHTML = mileage + textMileage;
}

function setIntCleanliness(cleanliness)
{
    intCleanDiv.className = parseCleanliness(cleanliness);
}

function setExtCleanliness(cleanliness)
{
    extCleanDiv.className = parseCleanliness(cleanliness);
}

function setCarPos(position)
{
    carPos = position;
}

function setCarBattery(battery)
{
    carBattery = battery;
    setBatteryText(battery);
}



/* Other functions */

// retrieve value based on cleanliness
function parseCleanliness(value)
{
    // value w25 exists but has no match in database
    var defaultClass = 'block-bar-value ';

    if (value == 'clean') {
        return defaultClass + 'w100';
    } else if (value == 'average') {
        return defaultClass + 'w75';
    } else if (value == 'dirty') {
        return defaultClass + 'w50';
    }

    return defaultClass + 'w100';
}
