// get the cars
$.get(invoicesUrl + "?date=" + lastPeriod, function (jsonData)
{
    var i = 0;
    var $table = $('#invoice-table-body');
    $table.empty();
    var columnClass = 'block-data-table-td cw-1-6';

    jsonData.data.forEach(function (invoice)
    {
        // create the table row
        var $row = $('<div>')
            .appendTo($table);
        $row.toggleClass('block-data-table-row clearfix ' + (i % 2) ? 'even' : 'odd');

        // create the invoiceNumber column
        var $invoiceNumberCol = $('<div>')
            .appendTo($row);
        $invoiceNumberCol.html(invoice['invoiceNumber']);
        $invoiceNumberCol.toggleClass(columnClass);

        // get day from invoiceDate
        var invoiceDay = invoice['invoiceDay'];
        invoiceDay -= (Math.round(invoiceDay / 100)) * 100;
        // create the day column
        var $dayCol = $('<div>')
            .appendTo($row);
        $dayCol.html(invoice['invoiceDay']);
        $dayCol.toggleClass(columnClass);

        var $typeCol = $('<div>')
            .appendTo($row);
        $typeCol.html(invoice['type']);
        $typeCol.toggleClass(columnClass);

        var $partialAmountCol = $('<div>')
            .appendTo($row);
        $partialAmountCol.html(invoice['content']['amounts']['total']);
        $partialAmountCol.toggleClass(columnClass);

        var $totalAmountCol = $('<div>')
            .appendTo($row);
        $totalAmountCol.html(invoice['content']['amounts']['grand_total']);
        $totalAmountCol.toggleClass(columnClass);

        var $downloadCol = $('<div>')
            .appendTo($row);
        $downloadCol.html(
            '<span class=link-to-download>' +
                '<a href=' + downloadLink + invoice['id'] + '>' +
                    '<i class"fa fa-download"></i> Download' +
                '</a>' +
            '</span>'
        );
        $downloadCol.toggleClass(columnClass);

    });
});
