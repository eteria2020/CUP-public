var isPopupVisible = false;
document.getElementById("close-btn").addEventListener('click', function (event)
{
    close();
});
document.getElementById("confirm-btn").addEventListener('click', function (event)
{
    triggerPayment();
});

function buy(packageId)
{
    // get the cars
    $.get(packageUrl + packageId, function (jsonData)
    {
        document.getElementById("description").innerHTML = jsonData.data.description;
        document.getElementById("minutes").innerHTML = jsonData.data.minutes;
        document.getElementById("cost").innerHTML = parseCost(jsonData.data.cost);
        document.getElementById("valid-from").innerHTML = jsonData.data.validFrom;
        document.getElementById("valid-to").innerHTML = jsonData.data.validTo;
        document.getElementById("available-until").innerHTML = jsonData.data.buyableUntil;
    });

    togglePopup();
}

function close()
{
    togglePopup();
}

function togglePopup()
{
    document.getElementById("pack-popup-container").style.display =
        isPopupVisible ? 'none' : 'inline';
    isPopupVisible = !isPopupVisible;
}

function parseCost(cost)
{
    return Math.floor(cost / 100) + ',' + cost % 100 + (cost % 100 < 10 ? '0' : '') + '\u20ac';
}

function triggerPayment()
{
    close();
}
