/* global $, ol,Geocoder, window, document, carsUrl, zonesUrl, carMarkerPathReserved, carMarkerPath,
 * textSearchAddress, poisMarkerPath, poisUrl, userEnabled, isLoggedIn, reservationsUrl, userId
 */
$(function () {
    "use strict";

    // Set the vector source; will contain the map data
    var vectorSourceVehicles = {},
        vectorSourceZones = {},
        vectorSourcePois = {};

    // Set the features collection
    var vehiclesFC = {},
        zonesFC = {},
        poisFC = {};

    // Define variables to interact with map elements
    var openInfoWindow = null;
    var resizeTimeout = null;
    // html elements
    var vehicleToggle = document.getElementById('cars-toggle-icon');
    var poiToggle = document.getElementById('energy-toggle-icon');
    // default Toggle State
    var vehicleMarkersSet = false;
    var poiMarkersSet = false;

    // GeoJSON Parser
    var format = new ol.format.GeoJSON();

    // Draw Functions
    var drawVehicle = function (vehiclePlate) {
        vectorSourceVehicles.addFeature(vehiclesFC[vehiclePlate]);
    };
    var drawZone = function (id) {
        vectorSourceZones.addFeature(zonesFC[id]);
    };
    var drawPoi = function (id) {
        vectorSourcePois.addFeature(poisFC[id]);
    };

    // Clear Functions
    var clearVehicles = function () {
        vectorSourceVehicles.clear();
    };
    var clearPois = function () {
        vectorSourcePois.clear();
    };

    // XHR Ajax Requests variables
    var xhrVehicles = null;
    var xhrPois = null;
    var xhrZones = null;

    // The MAP
    var OSM = new ol.layer.Tile({
        source: new ol.source.OSM()
    });

    vectorSourceVehicles = new ol.source.Vector({
        projection: "EPSG:3857",
        format: format
    });

    var clusterSourceVehicles = new ol.source.Cluster({
        distance: 60,
        source: vectorSourceVehicles
    });

    // POIS POPUP
    /**
     * Create an overlay to anchor the popup to the map.
     */
    var popupElement = document.getElementById("popup");
    var popup = new ol.Overlay({
        element: popupElement
    });
    var getHTMLPopup = function(
        address,
        town,
        province,
        zipCode
    ){
        return '<div id="popup" class="ol-popup">' +
            '<a href="#" id="popup-closer" class="ol-popup-closer"></a>' +
            '<div id="popup-content">' +
            'Address: ' + address + '<br>' +
            'Town: ' + town + '<br>' +
            'Province: ' + province + '<br>' +
            'ZIP Code: ' + zipCode +
            '</div></div>';
    };

    var vehiclesLayer = new ol.layer.Vector({
        source: clusterSourceVehicles,
        style: function (feature) {
            var size = feature.get('features').length;

            if (size === 1){
                return new ol.style.Style({
                    image: new ol.style.Icon(({
                        scale: 1,
                        anchor: [0.5, 1],
                        anchorXUnits: 'fraction',
                        anchorYUnits: 'fraction',
                        opacity: 1,
                        src: carMarkerPath
                    }))
                });
            } else {
                return new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 20,
                        stroke: new ol.style.Stroke({
                            color: '#ffffff'
                        }),
                        fill: new ol.style.Fill({
                            color: '#43a34c'
                        })
                    }),
                    text: new ol.style.Text({
                        textAlign: "center",
                        textBaseline: "middle",
                        font: 'Normal 16px Arial',
                        text: size.toString(),
                        fill: new ol.style.Fill({
                            color: '#ffffff'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#43a34c',
                            width: 1
                        }),
                        offsetX: 0,
                        offsetY: 0,
                        rotation: 0
                    })
                });
            }
        }
    });

    vectorSourceZones = new ol.source.Vector({
        projection: "EPSG:3857",
        format: format
    });

    var zonesLayer = new ol.layer.Vector({
        source: vectorSourceZones,
        style: new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(67, 163, 76, 0.3)'
            }),
            stroke: new ol.style.Stroke({
                color: '#f9ed33',
                width: 2
            })
        })
    });

    vectorSourcePois = new ol.source.Vector({
        projection: "EPSG:3857",
        format: format
    });

    var clusterSourcePois = new ol.source.Cluster({
        distance: 60,
        source: vectorSourcePois
    });

    var poisLayer = new ol.layer.Vector({
        source: clusterSourcePois,
        style: function (feature) {
            var size = feature.get('features').length;

            if (size === 1){
                return new ol.style.Style({
                    image: new ol.style.Icon(({
                        scale: 1,
                        anchor: [0.5, 1],
                        anchorXUnits: 'fraction',
                        anchorYUnits: 'fraction',
                        opacity: 1,
                        src: poisMarkerPathFree15
                    }))
                });
            } else {
                return new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 20,
                        stroke: new ol.style.Stroke({
                            color: '#ffffff'
                        }),
                        fill: new ol.style.Fill({
                            color: 'rgb(239,194,49)'
                        })
                    }),
                    text: new ol.style.Text({
                        textAlign: "center",
                        textBaseline: "middle",
                        font: 'Normal 16px Arial',
                        text: size.toString(),
                        fill: new ol.style.Fill({
                            color: '#ffffff'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'rgb(239,194,49)',
                            width: 1
                        }),
                        offsetX: 0,
                        offsetY: 0,
                        rotation: 0
                    })
                });
            }
        }
    });

    //var mapCenterLatitude = $('div.block-languages.block-menu ul li a.js-show-element').data('latitude');
    //var mapCenterLongitude = $('div.block-languages.block-menu ul li a.js-show-element').data('longitude');
    var fleetId = citta;
    			switch (citta) {
				case 1:
                                    var mapCenterLatitude = $('#Milano').data('latitude');
                                    var mapCenterLongitude = $('#Milano').data('longitude');
                                    $('#Milano').html(name);
                                    break;
				case 2:
                                    var mapCenterLatitude = $('#Firenze').data('latitude');
                                    var mapCenterLongitude = $('#Firenze').data('longitude');
                                    $('#Firenze').html(name);
                                    break;
				case 3:
                                    var mapCenterLatitude = $('#Roma').data('latitude');
                                    var mapCenterLongitude = $('#Roma').data('longitude');
        			    $('#Roma').html(name);
                                    break;
                                case 4:
                                    var mapCenterLatitude = $('#Modena').data('latitude');
                                    var mapCenterLongitude = $('#Modena').data('longitude');
        			    $('#Modena').html(name);
                                    break;
    }

    var view = new ol.View({
        // the view"s initial state
        center: ol.proj.transform([mapCenterLongitude, mapCenterLatitude], "EPSG:4326", "EPSG:3857"),
        zoom: 13
    });

    var map = new ol.Map({
        layers: [OSM, zonesLayer, poisLayer, vehiclesLayer],
        overlays: [popup],
        target: document.getElementById("map_canvas"),
        interactions: ol.interaction.defaults({ mouseWheelZoom: false }),
        controls: ol.control.defaults({
            attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
                collapsible: false
            })
        }),
        view: view
    });


    //Instantiate with some options and add the Control
    var geocoder = new Geocoder('nominatim', {
        provider: 'photon',
        lang: 'it',
        placeholder: textSearchAddress + '...',
        limit: 5,
        debug: true,
        autoComplete: true,
        keepOpen: true
    });
    map.addControl(geocoder);

    //////////////////// ICON /////////////////////////
    // toggle icon color
    function toggleButtonColor(icon, flag) {
        icon.style.backgroundImage = "url('../images/images" + (flag ? '' : '-grey') + ".png')";
    }

    // toggle icons off
    toggleButtonColor(vehicleToggle, vehicleMarkersSet);
    toggleButtonColor(poiToggle, poiMarkersSet);

    // set click event listeners
    vehicleToggle.addEventListener('click', function() {
        vehiclesLayer.setVisible(!vehicleMarkersSet);
        vehicleMarkersSet = !vehicleMarkersSet;
        toggleButtonColor(vehicleToggle, vehicleMarkersSet);
    });

    poiToggle.addEventListener('click', function() {
        poisLayer.setVisible(!poiMarkersSet);
        poiMarkersSet = !poiMarkersSet;
        toggleButtonColor(poiToggle, poiMarkersSet);
    });

    var addMapLoader = function() {
        $("#map_wrapper").prepend(
            "<div class=\"load-overlay\"><div>" +
            "<p><i class=\"fa fa-refresh fa-spin\">" +
            "</i></p></div></div>"
        );
    };
    var removeMapLoader = function() {
        $("#map_wrapper > div.load-overlay").remove();
    };

    //////////////////// DATA LOAD ////////////////////

    var loadVehicles = function(fleet) {
        // Clear Vehicles Features
        clearVehicles();

        // Add Map Loader (remove other ones)
        removeMapLoader();
        addMapLoader();

        // Abort Other Vehicles Load
        if (xhrVehicles !== null) {
            xhrVehicles.abort();
        }

        xhrVehicles = $.get('cars-api/' + fleet, function(vehicles) {
            $.each(vehicles, function(index, vehicle) {
                // position of the vehicle
                var latitude = parseFloat(vehicle.latitude);
                var longitude = parseFloat(vehicle.longitude);
                var intCleanliness = vehicle.intCleanliness;
                var extCleanliness = vehicle.extCleanliness;
                var battery = vehicle.battery;
                var plate = vehicle.plate;
                
                var bonus_car = "";
		var b_car = vehicle.bonus;
		for(var ib=0;ib<b_car.length;ib++){
                    if((b_car[ib].type=="nouse")&&(b_car[ib].value==15)&&(b_car[ib].status==true)){
                        bonus_car = "<br>I primi "+b_car[ib].value+" minuti di guida sono gratuiti";
                    }
		}
		//document.getElementById("bonus_message").innerHTML = bonus_car;
		//document.getElementById("bonus_message").style.fontWeight="normal";
                
                // Create the Vehicle Feature
                vehiclesFC[plate] = new ol.Feature({
                    geometry: new ol.geom.Point(
                        ol.proj.transform(
                            [longitude, latitude],
                            'EPSG:4326', 'EPSG:3857'
                        )
                    ),
                    image: new ol.style.Icon(({
                        scale: 1,
                        anchor: [0.5, 1],
                        anchorXUnits: 'fraction',
                        anchorYUnits: 'fraction',
                        opacity: 1,
                        src: poisMarkerPathFree15
                    })),
                    intClean: intCleanliness,
                    extClean: extCleanliness,
                    battery: battery,
                    type: "vehicle"
                });
                vehiclesFC[plate].setId(plate);

                drawVehicle(plate);
            });
            vehiclesLayer.setVisible(true);
            vehicleMarkersSet = true;
            toggleButtonColor(vehicleToggle, vehicleMarkersSet);
            removeMapLoader();
        });
    };

    xhrZones = $.ajax({
        url: zonesUrl,
        type: "POST",
        dataType: "json"
    }).success(function(data){
        $.each(data, function( index, zone) {
            if (typeof zone.id !== "undefined") {
                var id = zone.id;
                var zoneGeometry = zone.areaUse;
                var name = zone.name;

                // Create the new feature object
                zonesFC[id] = new ol.Feature({
                    geometry: format.readGeometry(
                        zoneGeometry,
                        {featureProjection: 'EPSG:3857'}
                    ),
                    name: name,
                    type: "zone"
                });
                zonesFC[id].setId(id);

                drawZone(id);
            }
        });
    });

    var loadPois = function(fleet){
        clearPois();

        if (xhrPois !== null) {
            xhrPois.abort();
        }

        xhrPois = $.get('pois/' + fleet, function(pois) {
            poisLayer.setVisible(false);
            poiMarkersSet = false;
            toggleButtonColor(poiToggle, poiMarkersSet);

            $.each(pois, function( index, poi) {
                if (typeof poi.id !== "undefined") {
                    var id = poi.id;
                    var latitude = parseFloat(poi.latitude);
                    var longitude = parseFloat(poi.longitude);
                    var name = poi.name;
                    var type = poi.type;
                    var code = poi.code;
                    var address = poi.address;
                    var town = poi.town;
                    var zipCode = poi.zipCode;
                    var province = poi.province;

                    // Create the new feature object
                    poisFC[id] = new ol.Feature({
                        geometry: new ol.geom.Point(
                            ol.proj.transform(
                                [longitude, latitude],
                                'EPSG:4326', 'EPSG:3857'
                            )
                        ),
                        name: name,
                        poiType: type,
                        code: code,
                        address: address,
                        town: town,
                        zipCode: zipCode,
                        province: province,
                        type: "poi"
                    });
                    poisFC[id].setId(id);

                    drawPoi(id);
                }
            });
        });
    };

    // Get Features
    loadVehicles(fleetId);
    loadPois(fleetId);

    // Bind Change Fleet
    $('ul.js-collapse-box.block-available-languages li a').click(function () {
        var latitude = parseFloat($(this).data('latitude')),
            longitude = parseFloat($(this).data('longitude')),
            name = $(this).data('name'),
            id = $(this).data('id');

        // change the name in the menu
        $('div.block-languages.block-menu ul li a.js-show-element span').html(name);
        $('div.block-languages.block-menu a.js-show-element').data("id", id);

        if (fleetId !== id){
            // Load new features
            loadVehicles(id);
            loadPois(id);
            fleetId = id;
        }

        // move the map
        if (typeof map !== 'undefined') {
            view.setCenter(
                ol.proj.transform(
                    [longitude, latitude],
                    'EPSG:4326', 'EPSG:3857'
                )
            );
        }

        // close the dropdown sending a click to the above menu
        $(".js-show-element").click();

        // set the preference in a cookie
        Cookies.set("sharengo_map_fleetPreference", id, {expires: 30});
    });


    // changes the reservation button's state
    var setReservationButton = function (plate, isCarBusy) {
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
    };


    // Map Vehicle click
    // display popup on click
    map.on('click', function(evt) {
        var feature = map.forEachFeatureAtPixel(
            evt.pixel, function(f) {return f;}
        );

        // Close Other Popup
        $(popup.getElement()).popover('destroy');

        if (feature) {
            if (typeof feature.get("features") === "object" &&
                feature.get("features").length === 1) {
                feature = feature.get("features")[0];
            }

            var type = feature.get('type');

            if (type === "vehicle") {
                // if an infowindow is open, close it
                if (openInfoWindow !== null) {
                    openInfoWindow.close();
                }

                var plate = feature.getId();
                var extClean = feature.get('extClean');
                var intClean = feature.get('intClean');
                var battery = feature.get('battery');
                var coordinates = ol.proj.transform(
                    feature.getGeometry().getCoordinates(),
                    "EPSG:3857",
                    "EPSG:4326"
                );

                // modify the elements
                setPlateText(plate);
                setIntCleanliness(intClean);
                setExtCleanliness(extClean);
                setCarBattery(battery);
                setCarPos(coordinates);

                // get the location and set it in the popup
                getAddress(coordinates[1], coordinates[0], function(results, status) {
                    console.log(results);
                    if (typeof results.display_name !== "undefined") {
                        setLocationText(results.display_name);
                    }
                });

                // show the popup
                showPopup(plate, feature);

                // Set the main button's behavior
                setReservationButton(plate, false);
            } else if (type === "poi") {
                var coordinates = feature.getGeometry().getCoordinates();
                var title = feature.get('name');
                var address = feature.get('address');
                var town = feature.get('town');
                var province = feature.get('province');
                var zipCode = feature.get('zipCode');
                var element = popup.getElement();
                var coordinate = evt.coordinate;

                $(element).popover('destroy');
                popup.setPosition(coordinates);
                // the keys are quoted to prevent renaming in ADVANCED mode.
                $(element).popover({
                    placement: 'top',
                    animation: false,
                    html: true,
                    content: getHTMLPopup(
                        address,
                        town,
                        province,
                        zipCode
                    ),
                    title: title
                });
                $(element).popover('show');
            }
        }
    });

    // Reverse Geocode
    var getAddress = function name(latitude, longitude, callback) {
        $.get('http://nominatim.openstreetmap.org/reverse?format=json&lat=' +
            latitude + '&lon=' + longitude + '&addressdetails=1' + '&accept-language=it',
            callback
        );
    };

    // Map Reseize
    var resize = function () {
        var newHeight = $('div.module-car-map').height();
        $('#map_canvas').css("height", newHeight - 20);
        map.updateSize();
    };

    // Map Resize Action Bind
     $('.js-toggle-map-height').on('click', function(e) {
        e.preventDefault();

        $('.module-car-map').toggleClass("small-height");
        $(this).toggleClass("active");

        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(resize, 500);
    });

    // Window Resize
    $(window).resize(function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(resize, 500);
    });

    // Set to the map the current page height
    resize();
});
