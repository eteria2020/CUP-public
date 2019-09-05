async function checkTransaction(uri, htmlMessageOk, htmlMessageKo, htmlMessageWait) {
    var promise;
    var loop = true;

    while(loop){

        promise = $.get(uri, function (json) {
            console.log(json);
            var message = null;

            if(json["outcome"]=="OK") {
                message = htmlMessageOk;
            } else if(json["outcome"]=="KO") {
                message = htmlMessageKo;
            } else {
                if(htmlMessageWait!=null) {
                    $("#transaction_message").html(htmlMessageWait);
                }
            }

            if(message!=null) {
                $("#transaction_message").html(message);
                loop = false;
            }

        });

        await sleep(3000);

    }
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
