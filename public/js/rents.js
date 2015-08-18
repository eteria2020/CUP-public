
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
            addRow(
                (i + 1) % 2,
                trip['timestampBeginningString'].substr(0, 10),
                trip['timestampBeginningString'].substr(11, 8),
                trip['latitudeBeginning'],
                trip['latitudeEnd'],
                'netto',
                'iva',
                'totale'
            );
            console.log(trip);
        });
    });
}

function resetTable()
{
    $('#rents-table-body').empty();
}

var columnClass1 = 'block-data-table-td';
var columnClass2 = 'cw-1-6';
var columnClass3 = 'table-row-fix';
function addRow(odd, date, hour, lat, lng, total, iva, grandTotal)
{

        // create the table row
        var $row = $('<div>');
        $row.addClass('block-data-table-row');
        $row.addClass('clearfix');
        $row.addClass((odd) ? 'odd' : 'even');

        // create the date column
        var $dateCol = $('<div>')
            .appendTo($row);
        $dateCol.html(date);
        $dateCol.addClass(columnClass1);
        $dateCol.addClass(columnClass2);
        $dateCol.addClass(columnClass3);

        // create the hour column
        var $hourCol = $('<div>')
            .appendTo($row);
        $hourCol.html(hour);
        $hourCol.addClass(columnClass1);
        $hourCol.addClass(columnClass2);
        $hourCol.addClass(columnClass3);

        // create the start column
        var $startCol = $('<div>')
            .appendTo($row);
        $startCol.html('');
        $startCol.addClass(columnClass1);
        $startCol.addClass(columnClass2);
        $startCol.addClass(columnClass3);

        // create the partial amount column
        var $partialAmountCol = $('<div>')
            .appendTo($row);
        $partialAmountCol.html(total);
        $partialAmountCol.addClass(columnClass1);
        $partialAmountCol.addClass(columnClass2);
        $partialAmountCol.addClass(columnClass3);

        // create the total amount column
        var $ivaCol = $('<div>')
            .appendTo($row);
        $ivaCol.html(iva);
        $ivaCol.addClass(columnClass1);
        $ivaCol.addClass(columnClass2);
        $ivaCol.addClass(columnClass3);

        // create the total amount column
        var $totalAmountCol = $('<div>')
            .appendTo($row);
        $totalAmountCol.html(grandTotal);
        $totalAmountCol.addClass(columnClass1);
        $totalAmountCol.addClass(columnClass2);
        $totalAmountCol.addClass(columnClass3);

        $row.appendTo($('#rents-table-body'));

        var latlng = new google.maps.LatLng(lat, lng);

        geocoder.geocode({'latLng': latlng}, function(results, status)
        {
            if (status == google.maps.GeocoderStatus.OK) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        $startCol.html(results[1].formatted_address);
                    }
                }
            }
        });
}
