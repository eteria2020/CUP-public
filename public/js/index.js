/* global document jQuery $ google carsUrl carMarkerPathReserved carMarkerPath
    zonesUrl poisUrl poisMarkerPath setPlateText setIntCleanliness
    setExtCleanliness setCarBattery setCarPos setLocationText showPopup
    userEnabled isLoggedIn reservationsUrl userId setReserveText textCarOccupied
    textCarReserved textCarReserve setAction textRegister setReserveButton */

var initialize, drawCoverage, map;

$(function () {
    "use strict";

    // set to true to enable top-right buttons
    var isInit = false;

    // get html elements
    var carsToggle = document.getElementById('cars-toggle-icon');
    var energyToggle = document.getElementById('energy-toggle-icon');

    // define variables to interact with map elements
    var carMarkers = [];
    var carMarkersSet = false;
    var energyMarkers = [];
    var energyMarkersSet = false;
    var openInfoWindow = null;
    var circle = null;

    /* Start */

    // asynchronously Load the map API
    jQuery(function() {
        var script = document.createElement('script');
        script.src = "//maps.googleapis.com/maps/api/js?sensor=false&libraries=places&callback=initialize";
        document.body.appendChild(script);
    });

    // remove any drawn circle
    function removeCoverage() {
        if (circle !== null) {
            circle.setMap(null);
        }
    }

    // draw a circle around the passed position based on battery (max 45km)
    drawCoverage = function(position, battery) {
        var circleOptions = {
            strokeColor: '#43a34c',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#43a34c',
            fillOpacity: 0.35,
            map: map,
            center: position,
            radius: 450 * battery // in meters
        };
        circle = new google.maps.Circle(circleOptions);
        circle.setMap(map);
    };

    // define on click function
    function toggleMarkers(markers, value) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(value);
        }
    }

    // toggle icon color
    function toggleButtonColor(icon, flag) {
        icon.style.backgroundImage = "url('../images/images" + (flag ? '' : '-grey') + ".png')";
    }

    // content to be shown in infowindow
    function getInfowindowContent(type, address) {
        return '<div>' +
            '<h2>' + type + '</h2>' +
            '<p>' + address + '</p>' +
            '</div>';
    }

    // changes the reservation button's state
    function setReservationButton(plate, isCarBusy) {
        if (userEnabled) {
            if (isLoggedIn) {
                // user is logged in
                $.get(reservationsUrl + '?plate=' + plate, function(jsonData) {

                    var isReserved = false;
                    var isReservedByMe = false;
                    var reservationId = '';

                    if (typeof jsonData.data[0] !== 'undefined' && jsonData.data[0] !== null) {
                        // there is an active reservation
                        if (jsonData.data[0].customer === userId) {
                            // there is an active reservation from the user
                            isReservedByMe = true;
                            reservationId = jsonData.data[0].id;
                        } else {
                            // there is an active reservation from another user
                            isReserved = true;
                        }
                    }

                    if (isCarBusy || (isReserved && !isReservedByMe)) {
                        // car cannot be reserved by user
                        setReserveText(textCarOccupied, false);
                        setAction(0, reservationId);
                    } else if (isReservedByMe) {
                        // reservation can be removed by user
                        setReserveText(textCarReserved, true);
                        setAction(2, reservationId);
                    } else {
                        // car can be reserved
                        setReserveText(textCarReserve, true);
                        setAction(1, reservationId);
                    }
                });
            } else {
                // user is not logged in
                setReserveText(textRegister, true);
            }
        } else {
            setReserveButton(userEnabled);
        }
    }

    // initialize the whole logic when map is loaded
    initialize = function() {
        /* Show the markers */

        // define the geocoder
        var geocoder = new google.maps.Geocoder();
        // define the initial position
        var latitude = $('div.block-languages.block-menu ul li a.js-show-element').data('latitude');
        var longitude = $('div.block-languages.block-menu ul li a.js-show-element').data('longitude');
        var myLatlng = new google.maps.LatLng(latitude, longitude);
        // define map options
        var mapOptions = {
            center: myLatlng, // Set our point as the centre location
            zoom: 14, // Set the zoom level
            scrollwheel: false,
            mapTypeId: 'roadmap', // set the default map type
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_BOTTOM
            },
            streetViewControl: true,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.LEFT_BOTTOM
            }
        };

        // display the map on the page
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

        // input find address
        var inputFindAddress = document.getElementById('find-address');
        if (inputFindAddress !== null)
        {
            var searchBox = new google.maps.places.SearchBox(inputFindAddress);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(inputFindAddress);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function() {
              searchBox.setBounds(map.getBounds());
            });

            var markers = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();

                if (places.length === 0) {
                  return;
                }

                // Clear out the old markers.
                markers.forEach(function(marker) {
                  marker.setMap(null);
                });
                markers = [];

                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    var icon = {
                      url: place.icon,
                      size: new google.maps.Size(71, 71),
                      origin: new google.maps.Point(0, 0),
                      anchor: new google.maps.Point(17, 34),
                      scaledSize: new google.maps.Size(25, 25)
                    };

                    // Create a marker for each place.
                    markers.push(new google.maps.Marker({
                        map: map,
                        icon: icon,
                        title: place.name,
                        position: place.geometry.location,
                        zIndex: google.maps.Marker.MAX_ZINDEX + 1
                    }));

                    if (place.geometry.viewport) {
                      // Only geocodes have viewport.
                      bounds.union(place.geometry.viewport);
                    } else {
                      bounds.extend(place.geometry.location);
                    }
                });
              map.fitBounds(bounds);
              map.setZoom(17);
            });

        }

        // get the cars
        $.get(carsUrl, function(jsonData) {
            // set a marker for each car
            jsonData.data.forEach(function(car) {
                // position of the car
                var latlng = new google.maps.LatLng(car.latitude, car.longitude);

                // create the marker on the map
                var marker = new google.maps.Marker(
                    {
                        position: latlng,
                        map: map,
                        icon: (car.isReservedByCurrentUser === true) ? carMarkerPathReserved : carMarkerPath
                    });

                // add event listener for when the marker is clicked
                google.maps.event.addListener(marker, 'click', function() {

                    // if an infowindow is open, close it
                    if (openInfoWindow !== null) {
                        openInfoWindow.close();
                    }

                    // if some car's coverage circle is drawn, remove it
                    removeCoverage();

                    // modify the elements
                    setPlateText(car.plate);
                    setIntCleanliness(car.intCleanliness);
                    setExtCleanliness(car.extCleanliness);
                    setCarBattery(car.battery);
                    setCarPos(marker.position);

                    // get the location and set it in the popup
                    geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK && results[0]) {
                            setLocationText(results[0].formatted_address);
                        }
                    });

                    // show the popup
                    showPopup(car, marker);

                    // Set the main button's behavior
                    setReservationButton(car.plate, car.busy);

                });

                // add the marker to the carMarkers array
                carMarkers.push(marker);

            });

            // car markers are set, enable toggle
            carMarkersSet = true;
            isInit = true;
            toggleButtonColor(carsToggle, carMarkersSet);
        });

        // Get the zones
        // Zone Object Scheleton
        var loadZoneObj =
            {
                "type": "FeatureCollection",
                "features": [
                    {
                        "type": "Feature",
                        "geometry": {}
                    }
                ]
            },
            allZonesObjs = {};
        $.ajax({
            url: zonesUrl,
            type: "POST",
            cache: true,
            dataType: "json"
        }).success(function(data) {
            $.each(data,
                function(id, val) {
                    if (typeof val.coordinates !== "undefined") {
                        // Create the new feature object
                        var newObj = $.extend(true, {}, loadZoneObj);
                        newObj.features[0].geometry = val;

                        // Inject the new feature in the map
                        allZonesObjs[id] = map.data.addGeoJson(newObj);
                        map.data.setStyle(
                            function() {
                                return {
                                    fillColor: "#43a34c",
                                    fillOpacity: 0.3,
                                    strokeColor: "#43a34c",
                                    strokeOpacity: 1,
                                    strokeWeight: 2,
                                    zIndex: 11
                                };
                            }
                        );
                    }
                }
            );
        });

        // get the pois
        $.get(poisUrl, function(jsonData) {
            // set a marker for each pois (default = hidden)
            jsonData.data.forEach(function(pois) {
                // show pois on map only if position is not 0,0
                if (pois.lon !== '0' && pois.lat !== '0') {
                    // position of the pois
                    var latlng = new google.maps.LatLng(pois.lat, pois.lon);

                    // create a marker
                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: null,
                        icon: poisMarkerPath
                    });

                    // create the infowindow
                    var infowindow = new google.maps.InfoWindow({
                        content: getInfowindowContent(pois.type, pois.address)
                    });

                    // add event listener for when the marker is clicked
                    google.maps.event.addListener(marker, 'click', function() {
                        // if an infowindow is open, close it
                        if (openInfoWindow !== null) {
                            openInfoWindow.close();
                        }
                        openInfoWindow = infowindow;
                        infowindow.open(map, marker);
                    });

                    energyMarkers.push(marker);
                }

            });

            isInit = true;
        });
    };

    /* Set the behavior of the top-right buttons */

    // toggle icons off
    toggleButtonColor(carsToggle, carMarkersSet);
    toggleButtonColor(energyToggle, energyMarkersSet);

    // set click event listeners
    carsToggle.addEventListener('click', function() {
        if (isInit) {
            toggleMarkers(carMarkers, (carMarkersSet ? null : map));
            carMarkersSet = !carMarkersSet;
            toggleButtonColor(carsToggle, carMarkersSet);
        }
    });

    energyToggle.addEventListener('click', function() {
        if (isInit) {
            toggleMarkers(energyMarkers, (energyMarkersSet ? null : map));
            energyMarkersSet = !energyMarkersSet;
            toggleButtonColor(energyToggle, energyMarkersSet);
        }
    });
});
