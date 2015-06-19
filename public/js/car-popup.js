// handle the second popup

// hold the km value in this var
var oldKm;
// text to be shown in second popup
var text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras nec magna rhoncus nunc varius euismod. Nullam interdum a augue sed accumsan. Fusce in quam leo. Pellentesque in tristique est.';

// get the necessary elements
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


// add the event listeners
btnBack.addEventListener('click', function(event)
{
    reset();
})
btnConfirm.addEventListener('click', function(event)
{
    confirm();
})

// create the functions
function nextStep()
{
    leftColumn.style.display = "none";
    rightColumn.style.width = "100%";
    btnReserve.style.display = "none"; //.hide,.show
    step2Buttons.style.display = "inline";
    circleIcon.className = "fa fa-info-circle";
    kmTitle.innerHTML = "Ricorda che:";
    oldKm = km.innerHTML;
    km.innerHTML = text;
}

function reset()
{
    leftColumn.style.display = "block";
    rightColumn.style.width = "";
    btnReserve.style.display = "inline";
    step2Buttons.style.display = "none";
    circleIcon.className = "fa fa-sun-o";
    kmTitle.innerHTML = "Autonomia";
    km.innerHTML = oldKm;
    isReservedDiv.innerHTML = '';
}

function confirm()
{
    var plate = document.getElementById('licence-plate').innerHTML;
    $.get(reserveUrl + plate, function (jsonData)
    {
        // TODO show return message
    });
}

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

// get the necessary elements
var div = document.getElementById('car-popup');

// add the event listener
$('#btn-close').click(function(e)
{
    div.style.display = "none";
    reset();
})
