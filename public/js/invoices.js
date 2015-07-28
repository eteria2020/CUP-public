refreshTable();

function refreshTable()
{
    $.get(invoicesUrl + "?date=" + lastPeriod, function (jsonData)
    {
        var i = 0;
        resetTable();
        var columnClass1 = 'block-data-table-td';
        var columnClass2 = 'cw-1-6';

        jsonData.data.forEach(function (invoice)
        {
            addRow(
                i % 2,
                invoice['invoiceNumber'],
                invoice['invoiceDate'],
                invoice['type'],
                invoice['content']['amounts']['total'],
                invoice['content']['amounts']['grand_total'],
                invoice['id']
            );
        });
    });
}

function resetTable()
{
    $('#invoice-table-body').empty();
}

var columnClass1 = 'block-data-table-td';
var columnClass2 = 'cw-1-6';
var columnClass3 = 'table-row-fix';
function addRow(odd, invoiceNumber, invoiceDate, type, total, grandTotal, id)
{
        // parse variables
        invoiceDate %= 100;
        total = Math.floor(total / 100) + ',' + parseDecimal(total % 100);
        grandTotal = Math.floor(grandTotal / 100) + ',' + parseDecimal(grandTotal % 100);

        // create the table row
        var $row = $('<div>');
        $row.addClass('block-data-table-row');
        $row.addClass('clearfix');
        $row.addClass((odd) ? 'odd' : 'even');

        // create the invoiceNumber column
        var $invoiceNumberCol = $('<div>')
            .appendTo($row);
        $invoiceNumberCol.html(invoiceNumber);
        $invoiceNumberCol.addClass(columnClass1);
        $invoiceNumberCol.addClass(columnClass2);
        $invoiceNumberCol.addClass(columnClass3);

        // create the day column
        var $dayCol = $('<div>')
            .appendTo($row);
        $dayCol.html(invoiceDate);
        $dayCol.addClass(columnClass1);
        $dayCol.addClass(columnClass2);
        $dayCol.addClass(columnClass3);

        // create the type column
        var $typeCol = $('<div>')
            .appendTo($row);
        $typeCol.html(type);
        $typeCol.addClass(columnClass1);
        $typeCol.addClass(columnClass2);
        $typeCol.addClass(columnClass3);

        // create the partial amount column
        var $partialAmountCol = $('<div>')
            .appendTo($row);
        $partialAmountCol.html(total);
        $partialAmountCol.addClass(columnClass1);
        $partialAmountCol.addClass(columnClass2);
        $partialAmountCol.addClass(columnClass3);

        // create the total amount column
        var $totalAmountCol = $('<div>')
            .appendTo($row);
        $totalAmountCol.html(grandTotal);
        $totalAmountCol.addClass(columnClass1);
        $totalAmountCol.addClass(columnClass2);
        $totalAmountCol.addClass(columnClass3);

        // create the download link column
        var $downloadCol = $('<div>')
            .appendTo($row);
        $downloadCol.html(
            '<span class=link-to-download>' +
                '<a href=' + downloadLink + id + '>' +
                    '<i class"fa fa-download"></i> Download' +
                '</a>' +
            '</span>'
        );
        $downloadCol.addClass(columnClass1);
        $downloadCol.addClass(columnClass2);
        $downloadCol.addClass(columnClass3);

        $row.appendTo($('#invoice-table-body'));
}

function parseDecimal(decimal)
{
    return ((decimal < 10) ? '0' : '') + decimal;
}
