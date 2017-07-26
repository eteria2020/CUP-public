
var script = document.createElement('script');
script.src = "//maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
document.body.appendChild(script);
var geocoder = null;

function initialize()
{
    geocoder = new google.maps.Geocoder();

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
