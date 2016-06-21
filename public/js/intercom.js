/* global window document intercomAppId intercomCustomerEmail intercomCustomerId $*/

var intercomSettings = {app_id: intercomAppId};

$(function () {
    'use strict';

    var w = window;
    var ic = w.Intercom;

    if (intercomCustomerEmail !== "") {
        intercomSettings.email = intercomCustomerEmail;
        intercomSettings.user_id = intercomCustomerId;
    }

    if (typeof ic === "function") {
        ic('reattach_activator');
        ic('update', intercomSettings);
    } else {
        var d = document;
        var i = function () {
            i.c(arguments);
        };
        var l = function () {
            var s = d.createElement('script');
            s.type = 'text/javascript';
            s.async = true;
            s.src = 'https://widget.intercom.io/widget/' + intercomAppId;

            var x = d.getElementsByTagName('script')[0];
            x.parentNode.insertBefore(s, x);
        };

        i.q = [];
        i.c = function(args) {
            i.q.push(args);
        };

        w.Intercom = i;

        if (w.attachEvent) {
            w.attachEvent('onload', l);
        } else {
            w.addEventListener('load', l, false);
        }
    }
});
