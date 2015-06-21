/* Global variables */

// hold the km value in this var
var oldKm;
// text to be shown in second popup
var text = 'Ricorda che dovrai avere con te lo smartphone per poter aprire l\'auto, oppure la tessera. Ti consigliamo di non utilizzare l\'auto se l\'autonomia è al di sotto del 20% di carica';

// get html elements
var div = document.getElementById('car-popup');
var btnClose = document.getElementById('btn-close');
var leftColumn = document.getElementById('left-column');
var rightColumn = document.getElementById('right-column');
var btnReserve = document.getElementById('btn-reserve');
var step2Buttons = document.getElementById('step2-buttons');
var circleIcon = document.getElementById('circle-icon');
var kmTitle = document.getElementById('km-title');
var kmText = document.getElementById('km');
var btnBack = document.getElementById('btn-back');
var btnConfirm = document.getElementById('btn-confirm');
var isReservedDiv = document.getElementById('reserve-text');
// elements modified on marker click event
var plateDiv = document.getElementById('licence-plate');
var intCleanDiv = document.getElementById('int_cleanliness');
var extCleanDiv = document.getElementById('ext_cleanliness');
var locationDiv = document.getElementById('location');
var kmDiv = document.getElementById('km');



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
    btnReserve.style.display = "none"; //.hide,.show
    step2Buttons.style.display = "inline";
    circleIcon.style.display = "none";
    kmTitle.innerHTML = "Ricorda che:";
    oldKm = km.innerHTML;
    km.innerHTML = text;
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
                // TODO show return message
            });
        }
    });
}



/* Create the other actions */

// reset the popup to the first screen
function reset()
{
    leftColumn.style.display = "block";
    rightColumn.style.width = "";
    btnReserve.style.display = "inline";
    step2Buttons.style.display = "none";
    circleIcon.style.display = "block";
    kmTitle.innerHTML = "Autonomia";
    km.innerHTML = oldKm;
}

// close the popup and reset some data
function close()
{
    div.style.display = "none";
    isReservedDiv.innerHTML = '';
    setAction(0);
    reset();
}

// confirm reservation
function confirm()
{
    var plate = document.getElementById('licence-plate').innerHTML;
    $.get(reserveUrl + plate, function (jsonData)
    {
        // TODO show return message
    });
}



// Setters

function setReserveText(text)
{
    isReservedDiv.innerHTML = text + ' <i class="fa fa-angle-right"></i>';
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
    kmDiv.innerHTML = text + ' km';
}

function setInnerCleanliness(cleanliness)
{
    intCleanDiv.className = parseCleanliness(cleanliness);
}

function setExtCleanliness(cleanliness)
{
    extCleanDiv.className = parseCleanliness(cleanliness);
}



// Other functions

// retrieve value based on cleanliness
function parseCleanliness(value)
{
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
