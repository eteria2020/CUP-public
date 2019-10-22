/* Slovak initialisation for the jQuery UI date picker plugin. */
/* Written by Vojtech Rinik (vojto@hmm.sk). */
jQuery(function($){
	$.datepicker.regional['sl'] = {
		closeText: 'Zapri',
		prevText: 'Prejšnji mesec',
		nextText: 'Naslednji mesec',
		currentText: 'danes',
		monthNames: ['Januar', 'februar', 'marca', 'april', 'maj', 'junij',
			'Julij', 'avgust', 'september', 'oktober', 'november', 'december'],
		monthNamesShort: ['Jan', 'februar', 'Mar', 'april', 'Máj', 'junij',
			'Julij', 'avgust', 'september', 'oktober', 'november', 'december'],
		dayNames: ['Nedelja', 'ponedeljek', 'torek', 'sreda', 'četrtek', 'petek', 'sobota'],
		dayNamesShort: ['Sun', 'po', 'Mar', 'Mer', 'Gio',' ven','so'],
		dayNamesMin: ['Ne','Po','Ut','St','Št','Pia','So'],
		weekHeader: 'Ty',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['sl']);
});
