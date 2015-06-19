// define global variables to interact with map elements
var map;
var carMarkers = [];
var carMarkersSet = false;
var energyMarkers = [];
var energyMarkersSet = false;

jQuery(function($)
{
    // Asynchronously Load the map API
    var script = document.createElement('script');
    script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
    document.body.appendChild(script);
});

function initialize()
{

    var geocoder = new google.maps.Geocoder(),
        myLatlng = new google.maps.LatLng(45.4627338,9.1777323),
        mapOptions =
        {
            center: myLatlng, // Set our point as the centre location
            zoom: 13, // Set the zoom level
            scrollwheel: false,
            mapTypeId: 'roadmap' // set the default map type
        },
        marker;

    // Display a map on the page
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    // Allow our satellite view have a tilted display (This only works for certain locations)
    map.setTilt(45);

    // retrieve value based on cleanliness
    function parseCleanliness(value)
    {
        var defaultClass = 'block-bar-value ';
        if (value == 'clean')
        {
            return defaultClass + 'w100';
        }
        else if (value == 'average')
        {
            return defaultClass + 'w75';
        }
        else if (value == 'dirty')
        {
            return defaultClass + 'w50';
        }
        return defaultClass + 'w100';
    }

    // get the positions of the cars
    $.get(carsUrl, function (jsonData)
    {
        jsonData.data.forEach(function (car)
        {

            var latlng = new google.maps.LatLng(car.lat, car.lon);

            var marker = new google.maps.Marker(
            {
                position: latlng,
                map: map,
                icon: carMarkerPath
            });

            google.maps.event.addListener(marker, 'click', function()
            {

                //retrieve the values for the car
                var plate = car['plate'];
                var intClean = parseCleanliness(car['intCleanliness']);
                var extClean = parseCleanliness(car['extCleanliness']);
                var km = car['km'] + ' km';
                var isBusy = car['busy'];
                
                // retrieve the elements that will be modified
                var plateDiv = document.getElementById('licence-plate');
                var intCleanDiv = document.getElementById('int_cleanliness');
                var extCleanDiv = document.getElementById('ext_cleanliness');
                var locationDiv = document.getElementById('location');
                var kmDiv = document.getElementById('km');

                // empty the location text
                locationDiv.innerHTML = '';

                // get the location
                geocoder.geocode({'latLng': latlng}, function(results, status)
                {
                    if (status == google.maps.GeocoderStatus.OK)
                    {
                        if (results[1])
                        {
                            locationDiv.innerHTML = results[1].formatted_address;
                        }
                        else
                        {
                            console.log('No results found');
                        }
                    }
                    else
                    {
                        console.log('Geocoder failed due to: ' + status);
                    }
                });

                // modify the elements
                plateDiv.innerHTML = plate;
                intCleanDiv.className = intClean;
                extCleanDiv.className = extClean;
                kmDiv.innerHTML = km;

                if (isLoggedIn)
                {
                    $.get(reservationsUrl, function (jsonData)
                    {

                        var isReserved = false; // TODO ??? how is this retrieved? 
                        var isReservedByMe = false;
                        if (typeof jsonData.data[0] !== 'undefined' && jsonData.data[0] !== null)
                        {
                            // TODO test with dummy reservation
                            if (jsonData.data[0].customer_id == userId)
                            {
                                isReservedByMe = true;
                            }
                        }

                        if (isBusy || (isReserved && !isReservedByMe))
                        {
                            isReservedDiv.innerHTML = 'L\'auto è occupata';
                        }
                        else if (isReservedByMe)
                        {
                            isReservedDiv.innerHTML = 'Annulla la prenotazione';
                            btnReserve.addEventListener('click', function(event)
                            {
                                    removeReservation();
                            });
                        }
                        else
                        {
                            isReservedDiv.innerHTML = 'Prenota l\'auto';
                            btnReserve.addEventListener('click', function(event)
                            {
                                    nextStep();
                            });
                        }
                    });
                }

                // show the popup
                document.getElementById('car-popup').style.display = "inline";

            });

            // add the marker to the carMarkers array
            carMarkers.push(marker);

        });
    });

    carMarkersSet = true;

    $.get(poisUrl, function (jsonData)
    {
        jsonData.data.forEach(function (pois)
        {

            var latlng = new google.maps.LatLng(pois.lat, pois.lon);

            var marker = new google.maps.Marker(
            {
                position: latlng,
                map: null,
                icon: poisMarkerPath
            });

            var contentString = '<div>' +
                                '<h2>' + pois.type + '</h2>' +
                                '<p>' + pois.address + '</p>' +
                                '</div>';

            var infowindow = new google.maps.InfoWindow(
            {
                content: contentString
            });

            google.maps.event.addListener(marker, 'click', function()
            {
                infowindow.open(map,marker);
            });

            energyMarkers.push(marker);

        });
    });

}

// handle the click on the top right buttons
var carsToggle = document.getElementById('cars-toggle');
var energyToggle = document.getElementById('energy-toggle');
var carsToggleIcon = document.getElementById('cars-toggle-icon');
var energyToggleIcon = document.getElementById('energy-toggle-icon');

// set energy icon off
toggleButtonColor(energyToggleIcon, energyMarkersSet);

carsToggle.addEventListener('click', function (event)
{
    toggleMarkers(carMarkers, (carMarkersSet ? null : map));
    carMarkersSet = !carMarkersSet;
    toggleButtonColor(carsToggleIcon, carMarkersSet);
})

energyToggle.addEventListener('click', function (event)
{
    toggleMarkers(energyMarkers, (energyMarkersSet ? null : map));
    energyMarkersSet = !energyMarkersSet;
    toggleButtonColor(energyToggleIcon, energyMarkersSet);
})

// define on click function
function toggleMarkers(markers, value)
{
    for (i=0; i<markers.length; i++)
    {
        markers[i].setMap(value);
    }
}

// toggle icon color
function toggleButtonColor(icon, flag)
{
    icon.style.backgroundImage = "url('../images/images" + (flag ? '' : '-grey') + ".png')";
}
