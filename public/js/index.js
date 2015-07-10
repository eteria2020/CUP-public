/* Global variables */

// set to true to enable top-right buttons
var isInit = false;

// get html elements
var carsToggle = document.getElementById('cars-toggle-icon');
var energyToggle = document.getElementById('energy-toggle-icon');

// define variables to interact with map elements
var map;
var carMarkers = [];
var carMarkersSet = false;
var energyMarkers = [];
var energyMarkersSet = false;
var openInfoWindow = null;
var circle = null;



/* Start */

// asynchronously Load the map API
jQuery(function($)
{
    var script = document.createElement('script');
    script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
    document.body.appendChild(script);
});

// initialize the whole logic when map is loaded
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
            zoom: 14, // Set the zoom level
            scrollwheel: false,
            mapTypeId: 'roadmap' // set the default map type
        };

    // sisplay the map on the page
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);



    // get the cars
    $.get(carsUrl + '?status=operative&active=true&busy=false&running=false', function (jsonData)
    {
        // set a marker for each car
        jsonData.data.forEach(function (car)
        {
            // show car on map only if operative and position is not 0,0
            if(car.latitude != '0' && car.longitude != '0' && !car.reservation && !car.busy)
            {
                // position of the car
                var latlng = new google.maps.LatLng(car.latitude, car.longitude);

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

                    // if an infowindow is open, close it
                    if(openInfoWindow !== null)
                    {
                        openInfoWindow.close();
                    }

                    // if some car's coverage circle is drawn, remove it
                    removeCoverage();

                    // modify the elements
                    setPlateText(car['plate']);
                    setIntCleanliness(car['intCleanliness']);
                    setExtCleanliness(car['extCleanliness']);
                    setCarBattery(car['battery']);
                    setCarPos(marker.position);

                    // get the location and set it in the popup
                    geocoder.geocode({'latLng': latlng}, function(results, status)
                    {
                        if (status == google.maps.GeocoderStatus.OK)
                        {
                            if (results[1])
                            {
                                setLocationText(results[1].formatted_address);
                            }
                        }
                    });

                    // Set the main button's behavior
                    //setReservationButton(car['busy']); // RESERVATION BUTTON

                    // show the popup
                    showPopup();

                });

                // add the marker to the carMarkers array
                carMarkers.push(marker);
            }

        });

        // car markers are set, enable toggle
        carMarkersSet = true;
        isInit = true;
        toggleButtonColor(carsToggle, carMarkersSet);
    });



    // get the pois
    $.get(poisUrl, function (jsonData)
    {
        // set a marker for each pois (default = hidden)
        jsonData.data.forEach(function (pois)
        {
            // show pois on map only if position is not 0,0
            if (pois.lon != '0' && pois.lat != '0')
            {
                // position of the pois
                var latlng = new google.maps.LatLng(pois.lon, pois.lat);

                // create a marker
                var marker = new google.maps.Marker(
                {
                    position: latlng,
                    map: null,
                    icon: poisMarkerPath
                });

                // create the infowindow
                var infowindow = new google.maps.InfoWindow(
                {
                    content: getInfowindowContent(pois.type, pois.address)
                });

                // add event listener for when the marker is clicked
                google.maps.event.addListener(marker, 'click', function()
                {
                    // if an infowindow is open, close it
                    if(openInfoWindow !== null)
                    {
                        openInfoWindow.close();
                    }
                    openInfoWindow = infowindow;
                    infowindow.open(map,marker);
                });

                energyMarkers.push(marker);
            }

        });

        isInit = true;
    });


}

/* Set the behavior of the top-right buttons */

// toggle icons off
toggleButtonColor(carsToggle, carMarkersSet);
toggleButtonColor(energyToggle, energyMarkersSet);

// set click event listeners
carsToggle.addEventListener('click', function (event)
{
    if (isInit)
    {
        toggleMarkers(carMarkers, (carMarkersSet ? null : map));
        carMarkersSet = !carMarkersSet;
        toggleButtonColor(carsToggle, carMarkersSet);
    }
});

energyToggle.addEventListener('click', function (event)
{
    if (isInit)
    {
        toggleMarkers(energyMarkers, (energyMarkersSet ? null : map));
        energyMarkersSet = !energyMarkersSet;
        toggleButtonColor(energyToggle, energyMarkersSet);
    }
});

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

// changes the reservation button's state
function setReservationButton(isCarBusy)
{
    if (isLoggedIn)
    {
        // TODO check if logic is correct
        $.get(reservationsUrl, function (jsonData)
        {

            var isReserved = false; // TODO - ??? how is this retrieved?
            var isReservedByMe = false;

            if (typeof jsonData.data[0] !== 'undefined' && jsonData.data[0] !== null)
            {
                // TODO test with dummy reservation
                if (jsonData.data[0].customer_id == userId)
                {
                    isReservedByMe = true;
                }
            }

            if (isCarBusy || (isReserved && !isReservedByMe))
            {
                setReserveText(textCarOccupied, false);
                setAction(0);
            }
            else if (isReservedByMe)
            {
                setReserveText(textCarReserved, true);
                setAction(2);
            }
            else
            {
                setReserveText(textCarReserve, true);
                setAction(1);
            }
        });
    }
    else
    {
        setReserveText(textRegister, true);
    }
}

// content to be shown in infowindow
function getInfowindowContent(type, address)
{
    return '<div>' +
            '<h2>' + type + '</h2>' +
            '<p>' + address + '</p>' +
            '</div>';
}

// draw a 60 km circle around the passed position
function drawCoverage(position, battery)
{
    var circleOptions = {
      strokeColor: '#43a34c',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#43a34c',
      fillOpacity: 0.35,
      map: map,
      center: position,
      radius: 600 * battery // in meters
    };
    circle = new google.maps.Circle(circleOptions);
    circle.setMap(map);
}

// remove any drawn circle
function removeCoverage()
{
    if (circle !== null)
    {
        circle.setMap(null);
    }
}
