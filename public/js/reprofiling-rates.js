$(document).ready(function () {
    $('#reprofiling-confirm').click(function (e) {
        var option = $('input[name="option"]:checked').val(),
            url = $.attr(this, 'data-ajax-url');

        e.preventDefault();

        if (option !== undefined) {
            //save option value in database
            $.post(url, {
                option: option
            });

            switch (option) {
                case "1": // conferma accettazione
                    window.location.reload();
                    break;
                case "2": // ripetere ex-novo profilazione
                case "3": // non ho fatto profilazione
                    window.location.replace('http://www.equomobili.com');
                    break;
            }
        }
    });
});
