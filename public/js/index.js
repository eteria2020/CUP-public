/* Global variables */

// set to true to enable top-right buttons
var isInit = false;

// get html elements
var carsToggle = document.getElementById('cars-toggle');
var energyToggle = document.getElementById('energy-toggle');
var carsToggleIcon = document.getElementById('cars-toggle-icon');
var energyToggleIcon = document.getElementById('energy-toggle-icon');

// define variables to interact with map elements
var map;
var carMarkers = [];
var carMarkersSet = false;
var energyMarkers = [];
var energyMarkersSet = false;



/* Start */

// asynchronously Load the map API
jQuery(function($)
{
    var script = document.createElement('script');
    script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
    document.body.appendChild(script);
});

// initialize the whole logic
function initialize()
{
    /* Show the markers */

    // define the geocoder
    var geocoder = new google.maps.Geocoder();
    // define the initial position
    var myLatlng = new google.maps.LatLng(45.4627338,9.1777323);
    // define map options
    var mapOptions =
        {
            center: myLatlng, // Set our point as the centre location
            zoom: 13, // Set the zoom level
            scrollwheel: false,
            mapTypeId: 'roadmap' // set the default map type
        };

    // sisplay the map on the page
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

    // get the cars
    $.get(carsUrl, function (jsonData)
    {
        // set a marker for each car
        jsonData.data.forEach(function (car)
        {
            // position of the car
            var latlng = new google.maps.LatLng(car.lat, car.lon);

            // create the marker on the map
            var marker = new google.maps.Marker(
            {
                position: latlng,
                map: map,
                icon: carMarkerPath
            });

            // add event listener for when the marker is clicked
            google.maps.event.addListener(marker, 'click', function()
            {

                // modify the elements
                setPlateText(car['plate']);
                setIntCleanliness(car['intCleanliness']);
                setExtCleanliness(car['extCleanliness']);
                setKmText(car['km']);
                setLocationText('');

                // get the location and set it in the popup
                geocoder.geocode({'latLng': latlng}, function(results, status)
                {
                    if (status == google.maps.GeocoderStatus.OK)
                    {
                        if (results[1])
                        {
                            setLocationText(results[1].formatted_address);
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

                // Set the main button's behavior
                if (isLoggedIn)
                {
                    // TODO
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

                        if (car['busy'] || (isReserved && !isReservedByMe))
                        {
                            setReserveText('L\'auto è occupata');
                            setAction(0);
                        }
                        else if (isReservedByMe)
                        {
                            setReserveText('Annulla la prenotazione');
                            setAction(2);
                        }
                        else
                        {
                            setReserveText('Prenota l\'auto');
                            setAction(1);
                        }
                    });
                }
                else
                {
                    setReserveText('Registrati e prenota');
                }

                // show the popup
                document.getElementById('car-popup').style.display = "inline";

            });

            // add the marker to the carMarkers array
            carMarkers.push(marker);

        });
    
        // car markers are set, enable toggle
        carMarkersSet = true;
        isInit = true;
        toggleButtonColor(carsToggleIcon, carMarkersSet);
    });
    
    // get the pois
    $.get(poisUrl, function (jsonData)
    {
        // set a marker for each pois (default = hidden)
        jsonData.data.forEach(function (pois)
        {
            // position of the pois
            var latlng = new google.maps.LatLng(pois.lat, pois.lon);

            // create a marker
            var marker = new google.maps.Marker(
            {
                position: latlng,
                map: null,
                icon: poisMarkerPath
            });

            // define content of infowindow
            var contentString = '<div>' +
                                '<h2>' + pois.type + '</h2>' +
                                '<p>' + pois.address + '</p>' +
                                '</div>';

            // create the infowindow
            var infowindow = new google.maps.InfoWindow(
            {
                content: contentString
            });

            // add event listener for when the marker is clicked
            google.maps.event.addListener(marker, 'click', function()
            {
                infowindow.open(map,marker);
            });

            energyMarkers.push(marker);

        });

        isInit = true;
    });
    

}

/* Set the behavior of the top-right buttons */

// toggle icons off
toggleButtonColor(carsToggleIcon, carMarkersSet);
toggleButtonColor(energyToggleIcon, energyMarkersSet);

// set click event listeners
carsToggle.addEventListener('click', function (event)
{
    if(isInit)
    {   
        toggleMarkers(carMarkers, (carMarkersSet ? null : map));
        carMarkersSet = !carMarkersSet;
        toggleButtonColor(carsToggleIcon, carMarkersSet);
    }
})

energyToggle.addEventListener('click', function (event)
{
    if(isInit)
    {
        toggleMarkers(energyMarkers, (energyMarkersSet ? null : map));
        energyMarkersSet = !energyMarkersSet;
        toggleButtonColor(energyToggleIcon, energyMarkersSet);
    }
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
