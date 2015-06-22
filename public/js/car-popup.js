/* Global variables */

// hold the km value in this var
var oldKm;
// text to enable spinner
var spinner = '<i class="fa fa-circle-o-notch fa-spin"></i>';
// used to disable modifications when popup is closed
var isOpen = false;

// get html elements
var mainContainer = document.getElementById('car-popup');
var btnClose = document.getElementById('btn-close');
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
})
btnBack.addEventListener('click', function(event)
{
    reset();
})
btnConfirm.addEventListener('click', function(event)
{
    confirm();
})
btnDone.addEventListener('click', function(event)
{
    close();
})



/* Handle the btnReserve different actions */

// stores btnReserve click action's state
var actionNumber = 0; // states are: 0=inactive, 1=nextStep, 2=removeReservation

// call this function to set the action on btnReserve click event
function setAction(number)
{
    actionNumber = number;
}

// this is called when btnReserve is clicked
function startAction()
{
    if (actionNumber == 1)
    {
        nextStep();
    }
    else if (actionNumber == 2)
    {
        removeReservation();
    }

    //if actionNumber == 0 (or anything else) do nothing
}

btnReserve.addEventListener('click', function(event)
{
    startAction();
})

// next step to reserve car
function nextStep()
{
    leftColumn.style.display = "none";
    rightColumn.style.width = "100%";
    btnReserve.style.display = "none";
    step2Buttons.style.display = "inline";
    circleIcon.style.display = "none";
    setRightBottomBlockTitle(titleRemember, 2);
    oldKm = blockRightBottomText.innerHTML;
    blockRightBottomText.innerHTML = textRemember;
}

// remove a car reservation
function removeReservation()
{
    // TODO
    $.get(reservationsUrl, function (jsonData)
    {
        if (typeof jsonData.data[0] !== 'undefined' && jsonData.data[0] !== null)
        {
            reservationID = jsonData.jsonData[0].customer_id;
            $.get(removeReservationUrl + reservationID, function (jsonData)
            {
                if (true) // TODO verify response
                {
                    completed(textReservationRemoved);
                }
                else
                {
                    completed(textReservationRemovedNot);
                }
            });
        }
    });
}



/* Create the other actions */


// show popup
function showPopup()
{
    mainContainer.style.display = "inline";
    isOpen = true;
}

// reset the popup to the first screen
function reset()
{
    leftColumn.style.display = "block";
    rightColumn.style.width = "";
    //btnReserve.style.display = "inline"; // RESERVATION BUTTON
    step2Buttons.style.display = "none";
    circleIcon.style.display = "block";
    setRightBottomBlockTitle(titleMilage, 1);
    blockRightBottomText.innerHTML = oldKm;
}

// close the popup and reset some data
function close()
{
    mainContainer.style.display = "none";
    isReservedDiv.innerHTML = spinner;
    setAction(0);
    blockRightTopDiv.style.display = "block"; // TODO - CHECK
    btnDone.style.display = "none";
    setLocationText(spinner);
    reset();
    isOpen = false;
}

// confirm reservation
function confirm()
{
    /*
    var plate = document.getElementById('licence-plate').innerHTML;
    $.get(reserveUrl + plate, function (jsonData)
    {
        */
        if (true) // TODO verify response
        {
            completed(textReservationCompleted);
        }
        else
        {
            completed(textReservationCompletedNot); // TODO verify error message
        }
        /*
    });
    */
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



/* Setters */

function setReserveText(text, setIcon) // TODO - CHECK warning, ban, times
{
    if (isOpen)
    {
        isReservedDiv.innerHTML = text + (setIcon ? ' <i class="fa fa-angle-right"></i>' : ''); // TODO - CHECK <i class="fa fa-times"></i>');
    }
}

function setRightBottomBlockTitle(text, stepNumber)
{
    if (stepNumber == 1)
    {
        text = '<i id="circle-icon" class="fa fa-sun-o"></i> ' + text;
    }
    else if (stepNumber == 2)
    {
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

function setKmText(text)
{
    blockRightBottomText.innerHTML = text + ' km';
}

function setIntCleanliness(cleanliness)
{
    intCleanDiv.className = parseCleanliness(cleanliness);
}

function setExtCleanliness(cleanliness)
{
    extCleanDiv.className = parseCleanliness(cleanliness);
}



/* Other functions */

// retrieve value based on cleanliness
function parseCleanliness(value)
{
    // value w25 exists but has no match in database
    var defaultClass = 'block-bar-value ';
    if (value == 'clean')
    {
        return defaultClass + 'w100';
    }
    else if (value == 'average')
    {
        return defaultClass + 'w75';
    }
    else if (value == 'dirty')
    {
        return defaultClass + 'w50';
    }
    return defaultClass + 'w100';
}
