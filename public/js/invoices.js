// get the cars
console.log(invoicesUrl + "?shortDate=" + lastPeriod);
$.get(invoicesUrl + "?date=" + lastPeriod, function (jsonData)
{
    console.log(jsonData);
    jsonData.data.forEach(function (invoice)
    {
        console.log(invoice['id']);
    });
});
