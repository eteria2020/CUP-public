
var script = document.createElement('script');
script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
document.body.appendChild(script);
var geocoder = null;

function initialize()
{
    geocoder = new google.maps.Geocoder();
    refreshTable(lastPeriod);
}

$("#rents-filter-select").change(function()
{
    var selectedValue = $(this).find(":selected").val();
    refreshTable(selectedValue);
});

function refreshTable(period)
{
    $.get(rentsUrl + "?month=" + period, function (jsonData)
    {
        var i = 0;
        resetTable();
        var columnClass1 = 'block-data-table-td';
        var columnClass2 = 'cw-1-6';

        var tripsCount = jsonData.data.length;

        var grandTotal = 0;
        var grandTotalToPay = 0;

        jsonData.data.forEach(function (trip)
        {
            var tripPayment = trip['tripPayments'];
            var tripBonuses = trip['tripBonuses'];
            var tripFreeFares = trip['tripFreeFares'];
            
            var start = moment(trip['timestampBeginning']['date'], "YYYY-MM-DD HH:mm:ss");
            var end = moment(trip['timestampEnd']['date'], "YYYY-MM-DD HH:mm:ss");
            var timeDiff = end.diff(start);     // milliseconds
            var diffMinutes = Math.ceil(timeDiff / (1000 * 60));    //minutes
            
            var tripMinutes = diffMinutes;
            var parkingMinutes = Math.ceil(trip['parkSeconds'] / 60);
            var totalAmount = 'in elaborazione';
            var totalAmountValue = 0;
            var mustPay = 'in elaborazione';
            var mustPayValue = 0;

            // show FREE for trips before 05/07/2015
            $fifthjuly2015 = moment("2015-07-05 00:00:00", "YYYY-MM-DD HH:mm:ss");
            if ($fifthjuly2015.diff(start) > 0) {
                totalAmount = 'FREE';
                mustPay = 'FREE';
            }

            if (typeof tripPayment !== "undefined") {
                tripMinutes = tripPayment['tripMinutes'];
                parkingMinutes = tripPayment['parkingMinutes'];
                totalAmountValue = (tripPayment['totalCost'] / 100);
                totalAmount = totalAmountValue + ' \u20ac';
                paymentStatus = tripPayment['status'];
                mustPayValue = (paymentStatus == 'payed_correctly' || paymentStatus == 'invoiced') ? 0 : totalAmountValue;
                mustPay = mustPayValue + ' \u20ac';
            }

            grandTotal = grandTotal + totalAmountValue;
            grandTotalToPay = grandTotalToPay + mustPayValue;

            tripBonus = 0;
            if (typeof tripBonuses !== "undefined") {
                for(var i = 0; i < tripBonuses.length; i++) {
                    tripBonus += tripBonuses[0]['minutes'];
                }
            }
            tripFree = 0;
            if (typeof tripFreeFares !== "undefined") {
                for(var i = 0; i < tripFreeFares.length; i++) {
                    tripFree += tripFreeFares[0]['minutes'];
                }
            }
            
            // exclude trips less than 5 mins long
            if (tripMinutes >= 5) {
                addRow(
                    0,
                    trip['timestampBeginningString'],
                    trip['timestampEndString'],
                    tripMinutes,
                    parkingMinutes,
                    totalAmount,
                    mustPay,
                    trip['latitudeBeginning'],
                    trip['longitudeBeginning'],
                    trip['latitudeEnd'],
                    trip['longitudeEnd'],
                    tripBonus,
                    tripFree
                );

            }

            // after last line is rendered...
            if (--tripsCount == 0) {
                addFinalRow(
                    1,
                    grandTotal + ' \u20ac',
                    grandTotalToPay + ' \u20ac'
                );
            }
        });
    });
}

function resetTable()
{
    $('#rents-table-body').find('.block-data-table-row-group').remove();
}

var groupClass = 'block-data-table-row-group';
var clearfixClass = 'clearfix';
var datainfoClass = 'data-info';
var columnClass1 = 'block-data-table-td';
var columnClass2 = 'cw-1-6';
//var columnClass3 = 'table-row-fix';
var columnClass4 = 'cw-1-4';
var columnClass5 = 'cw-1-2';
var classCenter = 'text-center';
var classRight = 'text-right';
var cssBorderTop = 'border-top';
var hiddenRowClass = 'block-data-field';

function addRow(
    odd,
    startDate,
    endDate,
    tripMinutes,
    parkingMinutes,
    totalAmount,
    mustPay,
    latStart,
    lonStart,
    latEnd,
    lonEnd,
    bonusMinutes,
    freeMinutes
) {

        var latStartPrintable = latStart;
        if (latStart.length > 6) { latStartPrintable = latStart.substring(0, 6); }
        var lonStartPrintable = lonStart;
        if (lonStart.length > 6) { lonStartPrintable = lonStart.substring(0, 6); }
        var latEndPrintable = latEnd;
        if (latEnd.length > 6) { latEndPrintable = latEnd.substring(0, 6); }
        var lonEndPrintable = lonEnd;
        if (lonEnd.length > 6) { lonEndPrintable = lonEnd.substring(0, 6); }

        // create the group for all the rows in a block
        var $group = $('<div>')
            .appendTo($('#rents-table-body'));
        $group.addClass(groupClass);
        $group.addClass(clearfixClass);

            // create the visible row
            var $row = $('<div>')
                .appendTo($group);
            $row.addClass('block-data-table-row');
            $row.addClass(clearfixClass);
            $row.addClass((odd) ? 'odd' : 'even');

                // create the date column
                var $startDateCol = $('<div>')
                    .appendTo($row);
                $startDateCol.html(startDate);
                $startDateCol.addClass(columnClass1);
                $startDateCol.addClass(columnClass2);

                // create the hour column
                var $endDateCol = $('<div>')
                    .appendTo($row);
                $endDateCol.html(endDate);
                $endDateCol.addClass(columnClass1);
                $endDateCol.addClass(columnClass2);

                // create the start column
                var $tripMinutesCol = $('<div>')
                    .appendTo($row);
                $tripMinutesCol.html(tripMinutes);
                $tripMinutesCol.addClass(columnClass1);
                $tripMinutesCol.addClass(columnClass2);
                $tripMinutesCol.addClass(classCenter);

                // create the partial amount column
                var $parkingMinutesCol = $('<div>')
                    .appendTo($row);
                $parkingMinutesCol.html(parkingMinutes);
                $parkingMinutesCol.addClass(columnClass1);
                $parkingMinutesCol.addClass(columnClass2);
                $parkingMinutesCol.addClass(classCenter);

                // create the total amount column
                var $totalAmountCol = $('<div>')
                    .appendTo($row);
                $totalAmountCol.html(totalAmount);
                $totalAmountCol.addClass(columnClass1);
                $totalAmountCol.addClass(columnClass2);
                $totalAmountCol.addClass(classRight);

                // create the total amount column
                var $mustPayCol = $('<div>')
                    .appendTo($row);
                $mustPayCol.html(mustPay);
                $mustPayCol.addClass(columnClass1);
                $mustPayCol.addClass(columnClass2);
                $mustPayCol.addClass(classRight);

            // create the first hidden row
            var $hiddenRow1 = $('<div>')
                .appendTo($group);
            $hiddenRow1.addClass('block-data-table-row');
            $hiddenRow1.addClass(datainfoClass);
            $hiddenRow1.addClass(clearfixClass);

                // create the start address column
                var $startAddressCol = $('<div>')
                    .appendTo($hiddenRow1);
                $startAddressCol.html('');
                $startAddressCol.addClass(columnClass1);
                $startAddressCol.addClass(columnClass5);

                    var $startAddressSpan = $('<span>')
                        .appendTo($startAddressCol);
                    $startAddressSpan.html('Partenza: ');
                    $startAddressSpan.addClass(hiddenRowClass);

                $startAddressCol.html($startAddressCol.html() + '<a href="#">Lat: ' + latStartPrintable + ' - Lon: ' + lonStartPrintable + '</a>');
                $startAddressCol.click(function() {
                    loadMapPopup(latStart, lonStart);
                    return false;
                });

                // create the end address column
                var $endAddressCol = $('<div>')
                    .appendTo($hiddenRow1);
                $endAddressCol.html('');
                $endAddressCol.addClass(columnClass1);
                $endAddressCol.addClass(columnClass5);

                    var $endAddressSpan = $('<span>')
                        .appendTo($endAddressCol);
                    $endAddressSpan.html('Destinazione: ');
                    $endAddressSpan.addClass(hiddenRowClass);

                $endAddressCol.html($endAddressCol.html() + '<a href="#">Lat: ' + latEndPrintable + ' - Lon: ' + lonEndPrintable + '</a>');
                $endAddressCol.click(function() {
                    loadMapPopup(latEnd, lonEnd);
                    return false;
                });

            // create the second hidden row
            if (bonusMinutes !== 0 ||
                freeMinutes !== 0) {
                var $hiddenRow2 = $('<div>')
                    .appendTo($group);
                $hiddenRow2.addClass('block-data-table-row');
                $hiddenRow2.addClass(datainfoClass);
                $hiddenRow2.addClass(clearfixClass);

                    // create the start address column
                    var $bonusMinutesCol = $('<div>')
                        .appendTo($hiddenRow2);
                    $bonusMinutesCol.html('');
                    $bonusMinutesCol.addClass(columnClass1);
                    $bonusMinutesCol.addClass(columnClass5);

                        var $bonusMinutesSpan = $('<span>')
                            .appendTo($bonusMinutesCol);
                        $bonusMinutesSpan.html('Minuti bonus consumati: ' + bonusMinutes);
                        $bonusMinutesSpan.addClass(hiddenRowClass);

                    // create the end address column
                    var $freeMinutesCol = $('<div>')
                        .appendTo($hiddenRow2);
                    $freeMinutesCol.html('');
                    $freeMinutesCol.addClass(columnClass1);
                    $freeMinutesCol.addClass(columnClass5);

                        var $freeMinutesSpan = $('<span>')
                            .appendTo($freeMinutesCol);
                        $freeMinutesSpan.html('Minuti gratuiti fruiti: ' + freeMinutes);
                        $freeMinutesSpan.addClass(hiddenRowClass);

            }

        /*var latlngStart = new google.maps.LatLng(latStart, lonStart);
        var latlngEnd = new google.maps.LatLng(latEnd, lonEnd);

        geocoder.geocode({'latLng': latlngStart}, function(results, status)
        {
            if (status == google.maps.GeocoderStatus.OK) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        $startAddressCol.html($startAddressCol.html() + results[1].formatted_address);
                    }
                }
            }
        });

        geocoder.geocode({'latLng': latlngEnd}, function(results, status)
        {
            if (status == google.maps.GeocoderStatus.OK) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        $endAddressCol.html($endAddressCol.html() + results[1].formatted_address);
                    }
                }
            }
        });*/
}

function addFinalRow(
    odd,
    totalAmount,
    mustPay
) {
        // create the group for all the rows in a block
        var $group = $('<div>')
            .appendTo($('#rents-table-body'));
        $group.addClass(groupClass);
        $group.addClass(clearfixClass);
        $group.addClass(cssBorderTop);

        // create the visible row
        var $row = $('<div>')
            .appendTo($group);
        $row.addClass('block-data-table-row');
        $row.addClass(clearfixClass);
        $row.addClass((odd) ? 'odd' : 'even');

        // create first column
        var $startDateCol = $('<div>')
            .appendTo($row);
        $startDateCol.html('');
        $startDateCol.addClass(columnClass1);
        $startDateCol.addClass(columnClass2);

        // create second column
        var $endDateCol = $('<div>')
            .appendTo($row);
        $endDateCol.html('');
        $endDateCol.addClass(columnClass1);
        $endDateCol.addClass(columnClass2);

        // create third column
        var $tripMinutesCol = $('<div>')
            .appendTo($row);
        $tripMinutesCol.html('');
        $tripMinutesCol.addClass(columnClass1);
        $tripMinutesCol.addClass(columnClass2);

        // create fourth column
        var $parkingMinutesCol = $('<div>')
            .appendTo($row);
        $parkingMinutesCol.html('<strong>Totali periodo</strong>');
        $parkingMinutesCol.addClass(columnClass1);
        $parkingMinutesCol.addClass(columnClass2);

        // create the total amount column
        var $totalAmountCol = $('<div>')
            .appendTo($row);
        $totalAmountCol.html('<strong>' + totalAmount + '</strong>');
        $totalAmountCol.addClass(columnClass1);
        $totalAmountCol.addClass(columnClass2);
        $totalAmountCol.addClass(classRight);

        // create the total amount column
        var $mustPayCol = $('<div>')
            .appendTo($row);
        $mustPayCol.html('<strong>' + mustPay + '</strong>');
        $mustPayCol.addClass(columnClass1);
        $mustPayCol.addClass(columnClass2);
        $mustPayCol.addClass(classRight);

}

var $mapPopup = $('#map-popup');
$mapPopup.click(function() {
    hideMapPopup();
});

function loadMapPopup(lat, lng)
{
    $mapPopup.html(
    '<img id="map-popup-img" src="' +
    'https://www.google.it/maps/api/staticmap?center=' +
    lat + ',' + lng +
    '&zoom=16&sensor=false&size=800x600&markers=color:green%7C' +
    lat + ',' + lng +
    '" class="map-popup-img">');
    $mapPopup.show();
}

function hideMapPopup()
{
    $mapPopup.hide();
}
