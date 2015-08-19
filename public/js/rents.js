
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

        jsonData.data.forEach(function (trip)
        {
            var tripPayment = trip['tripPayment'];
            var tripMinutes = '/';
            var parkingMinutes = '/';
            var totalAmount = '/';
            var mustPay = '/';
            if (tripPayment !== null) {
                tripMinutes = tripPayment['tripMinutes'] + ' (min)';
                parkingMinutes = tripPayment['parkingMinutes'] + ' (min)';
                totalAmount = tripPayment['totalCost'] + ' (\u20ac)';
                mustPay = tripPayment['status'];
                mustPay = (status == 'payed_correctly' || status == 'invoiced') ? 'NO' : 'SI';
            }
            addRow(
                (i + 1) % 2,
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
                '/',
                '/'
            );
        });
    });
}

function resetTable()
{
    $('#rents-table-body').empty();
}

var groupClass = 'block-data-table-row-group';
var clearfixClass = 'clearfix';
var datainfoClass = 'data-info';
var columnClass1 = 'block-data-table-td';
var columnClass2 = 'cw-1-6';
var columnClass3 = 'table-row-fix';
var columnClass4 = 'cw-1-4';
var columnClass5 = 'cw-1-2';
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
                $startDateCol.addClass(columnClass3);

                // create the hour column
                var $endDateCol = $('<div>')
                    .appendTo($row);
                $endDateCol.html(endDate);
                $endDateCol.addClass(columnClass1);
                $endDateCol.addClass(columnClass2);
                $endDateCol.addClass(columnClass3);

                // create the start column
                var $tripMinutesCol = $('<div>')
                    .appendTo($row);
                $tripMinutesCol.html(tripMinutes);
                $tripMinutesCol.addClass(columnClass1);
                $tripMinutesCol.addClass(columnClass2);
                $tripMinutesCol.addClass(columnClass3);

                // create the partial amount column
                var $parkingMinutesCol = $('<div>')
                    .appendTo($row);
                $parkingMinutesCol.html(parkingMinutes);
                $parkingMinutesCol.addClass(columnClass1);
                $parkingMinutesCol.addClass(columnClass2);
                $parkingMinutesCol.addClass(columnClass3);

                // create the total amount column
                var $totalAmountCol = $('<div>')
                    .appendTo($row);
                $totalAmountCol.html(totalAmount);
                $totalAmountCol.addClass(columnClass1);
                $totalAmountCol.addClass(columnClass2);
                $totalAmountCol.addClass(columnClass3);

                // create the total amount column
                var $mustPayCol = $('<div>')
                    .appendTo($row);
                $mustPayCol.html(mustPay);
                $mustPayCol.addClass(columnClass1);
                $mustPayCol.addClass(columnClass2);
                $mustPayCol.addClass(columnClass3);

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
                $startAddressCol.addClass(columnClass3);

                    var $startAddressSpan = $('<span>')
                        .appendTo($startAddressCol);
                    $startAddressSpan.html('Partenza: ');
                    $startAddressSpan.addClass(hiddenRowClass);

                // create the end address column
                var $endAddressCol = $('<div>')
                    .appendTo($hiddenRow1);
                $endAddressCol.html('');
                $endAddressCol.addClass(columnClass1);
                $endAddressCol.addClass(columnClass5);
                $endAddressCol.addClass(columnClass3);

                    var $endAddressSpan = $('<span>')
                        .appendTo($endAddressCol);
                    $endAddressSpan.html('Destinazione: ');
                    $endAddressSpan.addClass(hiddenRowClass);

            // create the first hidden row
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
                $bonusMinutesCol.addClass(columnClass3);

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
                $freeMinutesCol.addClass(columnClass3);

                    var $freeMinutesSpan = $('<span>')
                        .appendTo($freeMinutesCol);
                    $freeMinutesSpan.html('Minuti gratuiti fruiti: ' + freeMinutes);
                    $freeMinutesSpan.addClass(hiddenRowClass);

        var latlngStart = new google.maps.LatLng(latStart, lonStart);
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
        });
}
