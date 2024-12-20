$(document).ready(function () {
    // Flash Message Display Function
    function showFlashMessage(message, type) {
        let flashId =
            type === "success"
                ? "#flash-message-success"
                : "#flash-message-error";
        let flashContentId =
            type === "success"
                ? "#flash-message-success-content"
                : "#flash-message-error-content";

        // Clear previous messages
        $(flashContentId).text(message);

        // Fade in the flash message
        $(flashId).fadeIn();

        // Auto-hide the message after 3 seconds
        setTimeout(function () {
            $(flashId).fadeOut();
        }, 3000);
    }

    // Data Store Form Submission
    $("#dataStoreform").submit(function (e) {
        e.preventDefault(); // Prevent default form submission
        let formData = new FormData(this); // Gather form data

        $.ajax({
            url: routes.datastore, // Server endpoint
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                showFlashMessage(response.data, "success");
                $("#dataStore").modal("hide"); // Hide the modal
            },
            error: function (xhr) {
                showFlashMessage("Error storing data", "error");
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $("#" + key + "_error")
                        .text(value[0])
                        .show();
                });
            },
        });
    });

    // Corporation Form Submission
    $("#storeCorporation").submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: routes.cbeStore,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                showUpdatedCbe(response.corporations);
                showFlashMessage(response.data, "success");
                $("#addCorporationModal").modal("hide");
            },
            error: function (xhr) {
                showFlashMessage("Error storing corporation", "error");
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $("#" + key + "_error")
                        .text(value[0])
                        .show();
                });
            },
        });
    });

    // Event delegation for update and delete buttons
    $(document).on("click", ".cbeUpdate", function () {
        const corporationId = $(this).data("id");
        const name = $(this).data("name");
        const email = $(this).data("email");
        const password = $(this).data("password");

        // Populate modal fields
        $("#updateCorporationModal #id").val(corporationId);
        $("#updateCorporationModal #update_name").val(name);
        $("#updateCorporationModal #update_email").val(email);
        $("#updateCorporationModal #update_password").val(password);

        // Show the modal
        $("#updateCorporationModal").modal("show");
    });

    $(document).on("click", ".cbeDelete", function () {
        const id = $(this).data("id");
        const deleteUrl = routes.cbeDelete.replace("mm", id);

        $.ajax({
            url: deleteUrl,
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                showFlashMessage(response.data, "success");
                showUpdatedCbe(response.corporations);
            },
            error: function (xhr) {
                showFlashMessage("Error deleting corporation", "error");
                let errors = xhr.responseJSON.errors;
                if (errors) {
                    $.each(errors, function (key, value) {
                        $("#" + key + "_error")
                            .text(value[0])
                            .show();
                    });
                } else {
                    showFlashMessage("An unexpected error occurred.", "error");
                }
            },
        });
    });

    // Update Corporation Submission
    $("#updateCorporation").submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $("#updateCorporation #id").val();

        $.ajax({
            url: `${routes.cbeUpdate}/${id}`,
            type: "PUT",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                showFlashMessage(response.data, "success");
                $("#updateCorporationModal").modal("hide");
                showUpdatedCbe(response.corporations);
            },
            error: function (xhr) {
                showFlashMessage("Error updating corporation", "error");
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $("#update_" + key + "_error")
                        .text(value[0])
                        .show();
                });
            },
        });
    });

    // Function to refresh corporation data
    function showUpdatedCbe(corporations) {
        let htmlContent = "";

        $.each(corporations, function (index, corporation) {
            htmlContent += `
        <div class="col-md-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">${corporation.name}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-4">
                        <li><i class="bi bi-envelope"></i> <strong>Email:</strong> ${corporation.email}</li>
                        <li><i class="bi bi-key"></i> <strong>Password:</strong> ${corporation.password}</li>
                    </ul>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-primary cbeUpdate"
                            data-id="${corporation.id}"
                            data-name="${corporation.name}"
                            data-email="${corporation.email}"
                            data-password="${corporation.password}">
                            <i class="bi bi-pencil-square"></i> Update
                        </button>

                         <button class="cbeDelete btn btn-danger" data-id="${corporation.id}">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        });

        // Update the container where you want to display the corporations
        $("#corporationContainer").html(htmlContent); // Ensure you have a container with this ID
    }
    function showUpdatedSurveyors(surveyors) {
        let htmlContent = "";

        $.each(surveyors, function (index, surveyor) {
            htmlContent += `
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">${surveyor.name}</h5>
                        <p class="card-text">
                            <strong>Email:</strong> ${surveyor.email} <br>
                            <strong>Mobile:</strong> ${surveyor.mobile} <br>
                            <strong>Data ID:</strong> ${surveyor.data_id} <br>
                            <strong>Password:</strong> ${surveyor.password} <br>
                            <strong>Password Reset Token:</strong> ${surveyor.password_reset_token}
                        </p>
                        <button class="btn btn-primary editSurveyor" data-id="${surveyor.id}"
                            data-name="${surveyor.name}" data-email="${surveyor.email}"
                            data-mobile="${surveyor.mobile}" data-password="${surveyor.password}"
                            data-data_id="${surveyor.data_id}">Update</button>
                        <button class="btn btn-danger delete-surveyor" data-id="${surveyor.id}">Delete</button>
                    </div>
                </div>
            </div>`;
        });

        // Update the container where you want to display the surveyors
        $(".show-surveyors").html(htmlContent); // Ensure you have a container with this class
    }

    //surveyors details
    $("#addSurveyorForm").on("submit", function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Gather form data
        var formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            url: routes.SurveyorStore, // Correctly formatted route
            type: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                // Handle success
                $("#addSurveyorModal").modal("hide");
                $("#addSurveyorForm")[0].reset();
                showFlashMessage(response.message, "success");
                showUpdatedSurveyors(response.surveyors);
            },
            error: function (xhr) {
                // Handle error
                showFlashMessage("Error ", "error");
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        const input = $(`#${key}`);
                        input.addClass("is-invalid"); // Add Bootstrap invalid class
                        input.next(".invalid-feedback").text(errors[key][0]); // Show error message
                    }
                } else {
                    alert("An unexpected error occurred. Please try again.");
                }
            },
        });
    });

    // Reset validation errors on modal clo
    $("#addSurveyorModal").on("hidden.bs.mseodal", function () {
        // Reset form fields
        $("#addSurveyorForm")[0].reset();

        // Remove invalid classes and clear error messages
        $("#addSurveyorForm .form-control").removeClass("is-invalid"); // Target form controls in this form only
        $("#addSurveyorForm .invalid-feedback").text(""); // Clear error messages
    });

    // Edit Surveyor
    $(document).on("click", ".editSurveyor", function () {
        const data_id = $(this).data("data_id");
        const id = $(this).data("id");
        const name = $(this).data("name");
        const email = $(this).data("email");
        const mobile = $(this).data("mobile");
        // const password = $(this).data('password');

        // Populate modal fields
        // $("#updateCorporationModal #id").val(id);
        $("#updateSurveyorModal #update_name").val(name);
        $("#updateSurveyorModal #update_email").val(email);
        // $("#updateSurveyorModal #update_password").val(password);
        $("#updateSurveyorModal #update_mobile").val(mobile);
        $("#updateSurveyorModal #update_data_id").val(data_id);
        $("#updateSurveyorModal #update_id").val(data_id);
        $("#updateSurveyorModal").modal("show");
    });
    $("#updateSurveyorForm").submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $("#updateSurveyorForm #update_id").val();
        alert(id);
        $.ajax({
            url: routes.surveyorUpdate,
            type: "POST", // Or change to "PUT" and update the route accordingly
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                showFlashMessage(response.data, "success");
                $("#updateCorporationModal").modal("hide");
                showUpdatedSurveyors(response.surveyors);
            },
            error: function (xhr) {
                showFlashMessage("Error updating surveyor", "error");
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $("#update_" + key + "_error")
                        .text(value[0])
                        .show();
                });
            },
        });
    });

    $(document).ready(function () {
        $(document).on("click", ".delete-surveyor", function () {
            const surveyorId = $(this).data("id");
            alert(surveyorId); // Check if this shows up
            const deleteUrl = routes.surveyorDelete.replace("mm", surveyorId);

            if (confirm("Are you sure you want to delete this surveyor?")) {
                $.ajax({
                    url: deleteUrl,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (response) {
                        showFlashMessage(
                            "Surveyor deleted successfully!",
                            "success"
                        );
                        console.log(response);
                        showUpdatedSurveyors(response.surveyors);
                        // Optionally refresh the surveyor list or remove the item from the UI
                    },
                    error: function (xhr) {
                        showFlashMessage("Error deleting surveyor", "error");
                    },
                });
            } else {
                console.log("Deletion canceled");
            }
        });
    });

    // Reset Modal Forms on Close
    $(".modal").on("hidden.bs.modal", function () {
        $(this).find("form")[0].reset(); // Reset form fields
        $(this).find(".invalid-feedback").hide(); // Hide error messages
    });

    //surveyors dashboard
    // Ensure extent is parsed correctly as a JavaScript array of floats
    // Parse the extent data into an array
    var extent = [
        parseFloat(extentData.left),
        parseFloat(extentData.bottom),
        parseFloat(extentData.right),
        parseFloat(extentData.top),
    ];

    // Calculate the center of the extent
    var extentCenter = ol.extent.getCenter(extent);

    // Initialize the OpenLayers map
    const map = new ol.Map({
        target: "map", // Target the div with id 'map'
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM(), // OpenStreetMap base layer
            }),
            new ol.layer.Image({
                source: new ol.source.ImageStatic({
                    url: imagepath, // Path to the static image
                    imageExtent: extent, // Define the extent of the image layer
                }),
            }),
        ],
        view: new ol.View({
            center: extentCenter, // Center the map on the image
            projection: "EPSG:3857", // Use EPSG:3857 projection (Web Mercator)
            zoom: 20, // Initial zoom level
        }),
    });

    // Create a new vector source for the user's location
    var userLocationSource = new ol.source.Vector();

    // Create a vector layer for the user's location
    var userLocationLayer = new ol.layer.Vector({
        source: userLocationSource,
        style: new ol.style.Style({
            image: new ol.style.Circle({
                radius: 8,
                fill: new ol.style.Fill({ color: "yellow" }), // Green fill for the circle
                stroke: new ol.style.Stroke({ color: "white", width: 2 }), // White border
            }),
        }),
    });

    // Add the user location layer to the map
    map.addLayer(userLocationLayer);

    // Function to update the user's location on the map
    function updateUserLocation(position) {
        const { latitude, longitude } = position.coords;

        // Convert the latitude and longitude to map coordinates
        const userCoordinates = ol.proj.fromLonLat([longitude, latitude]);

        // Clear any existing features
        userLocationSource.clear();

        // Add a new feature for the user's current location
        const userLocationFeature = new ol.Feature({
            geometry: new ol.geom.Point(userCoordinates),
        });
        userLocationSource.addFeature(userLocationFeature);

        // Optionally, center the map on the user's location
        map.getView().setCenter(userCoordinates);
        map.getView().setZoom(15); // Adjust zoom level as needed
    }

    // Function to handle errors while fetching location
    function handleLocationError(error) {
        console.error("Error fetching location:", error.message);
    }

    // Use the Geolocation API to watch the user's position
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            updateUserLocation, // Success callback
            handleLocationError, // Error callback
            {
                enableHighAccuracy: true, // Enable high-accuracy GPS
                maximumAge: 0, // No cached position
            }
        );
    } else {
        console.error("Geolocation API not supported by this browser.");
    }

    // Function to create point style
    function createPointStyle(feature) {
        var gisid = feature.get("gisid");
        var pointData = pointDatas.find((data) => data.point_gisid == gisid);

        return new ol.style.Style({
            image: new ol.style.Circle({
                radius: 7,
                fill: new ol.style.Fill({
                    color: pointData ? "red" : "blue", // Color based on data presence
                }),
                stroke: new ol.style.Stroke({
                    color: "#ffffff",
                    width: 1,
                }),
            }),
            text: new ol.style.Text({
                text: gisid || "",
                scale: 1.2,
                offsetY: -15,
                fill: new ol.style.Fill({
                    color: "#000000",
                }),
                stroke: new ol.style.Stroke({
                    color: "#ffffff",
                    width: 3,
                }),
            }),
        });
    }

    // Vector source for storing the features (points, lines, polygons)
    var vectorSource = new ol.source.Vector();

    // Vector layer to render the features on the map
    var vectorLayer = new ol.layer.Vector({
        source: vectorSource,
        style: function (feature) {
            var type = feature.get("type");
            console.log("Feature type:", type);
            var gisid = feature.get("gisid");
            if (type === "Polygon") {
                var polygonData = polygonDatas.find(
                    (data) => data.gisid == gisid
                );
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: polygonData ? "red" : "blue",
                        width: 4,
                    }),
                    fill: new ol.style.Fill({
                        color: polygonData
                            ? "rgba(255, 0, 0, 0.3)"
                            : "rgba(0, 0, 255, 0.3)", // Light red or light blue fill with 30% opacity
                    }),
                });
            } else if (type === "Point") {
                return createPointStyle(feature);
            } else if (type === "LineString" || type === "MultiLineString") {
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: "yellow",
                        width: 5,
                    }),
                });
            }
        },
    });

    // Add the vector layer to the map
    map.addLayer(vectorLayer);

    // Function to add features (points, lines, polygons) to the vector layer
    function addFeatures(features, type) {
        features.forEach(function (feature) {
            // Parse coordinates if they are in string format
            var coords = Array.isArray(feature.coordinates)
                ? feature.coordinates
                : JSON.parse(feature.coordinates || "[]");

            if (type === "Polygon") {
                coords.forEach(function (ring) {
                    var coordsForFeature = ring.map((coord) => [
                        coord[0],
                        coord[1],
                    ]);
                    var olFeature = new ol.Feature({
                        geometry: new ol.geom.Polygon([coordsForFeature]),
                        type: "Polygon",
                        gisid: feature.gisid,
                        area: feature.area,
                    });
                    vectorSource.addFeature(olFeature);
                });
            } else if (type === "Point") {
                var coordsForFeature = Array.isArray(feature.coordinates)
                    ? feature.coordinates
                    : JSON.parse(feature.coordinates || "[]");
                var olFeature = new ol.Feature({
                    geometry: new ol.geom.Point(coordsForFeature),
                    type: "Point",
                    gisid: feature.gisid,
                });
                vectorSource.addFeature(olFeature);
            } else if (type === "LineString" || type === "MultiLineString") {
                var olFeature;
                if (type === "LineString") {
                    var coordsForFeature = coords.map((coord) => [
                        coord[0],
                        coord[1],
                    ]);
                    olFeature = new ol.Feature({
                        geometry: new ol.geom.LineString(coordsForFeature),
                        type: type,
                    });
                } else if (type === "MultiLineString") {
                    var geometries = coords.map(
                        (lineCoords) => new ol.geom.LineString(lineCoords)
                    );
                    olFeature = new ol.Feature({
                        geometry: new ol.geom.MultiLineString(geometries),
                        type: type,
                    });
                }
                vectorSource.addFeature(olFeature);
            }
        });
    }

    // Function to refresh the vector layer with updated points, lines, and polygons
    function refreshLayer(points, lines, polygons) {
        console.log(points);
        vectorSource.clear(); // Clear existing features before adding new ones
        addFeatures(points, "Point");
        addFeatures(lines, "MultiLineString"); // Ensure type matches your data
        addFeatures(polygons, "Polygon");
    }

    // Refresh the layer with the data providedp
    refreshLayer(points, lines, polygons);

    // Optional: Console log to see the points data
    $(document).ready(function () {
        map.on("click", function (evt) {
            if ($("#addedFeature").val() == "none") {
                $(".error-message").text("");
                $("input").removeClass("is-invalid");
                $("select").removeClass("is-invalid");
                const feature = map.forEachFeatureAtPixel(
                    evt.pixel,
                    function (feature) {
                        return feature;
                    }
                );
                if (feature) {
                    var properties = feature.getProperties();

                    var geometryType = feature.getGeometry().getType();

                    if (geometryType == "Point") {
                        //if point
                        function resetFields(fieldIds) {
                            fieldIds.forEach(function (id) {
                                $("#" + id).val("");
                            });
                        }

                        // List of field IDs to reset
                        var fieldsToReset = [
                            "pointgis",
                            "assessment",
                            "old_assessment",
                            "owner_name",
                            "present_owner_name",
                            "floor",
                            "old_door_no",
                            "eb",
                            "new_door_no",
                            "bill_usage",
                            "water_tax",

                            "phone",
                            "remarks",
                        ];
                        // Reset the fields
                        resetFields(fieldsToReset);
                        var content = "";
                        for (var key in properties) {
                            // alert( key + ':</strong> ' + properties[key]);
                            if (key !== "geometry") {
                                content +=
                                    "<li><strong>" +
                                    key +
                                    ":</strong> " +
                                    properties[key] +
                                    "</li>";
                            }
                        }

                        var gisid = properties["gisid"];
                        $("#pointgis").val(gisid);
                        var polygonbData = polygonDatas.find(
                            (data) => data.gisid === gisid
                        );
                        console.log(polygonbData);
                        var polygonnumofbill = polygonbData
                            ? polygonbData.number_bill
                            : null;
                        var matchingPointsCount = pointDatas.filter(
                            (data) => data.point_gisid === gisid
                        ).length;
                        if (polygonnumofbill > matchingPointsCount) {
                            $("#pointModal").modal("show");
                        } else {
                            showFlashMessage(
                                "Already this building have " +
                                    matchingPointsCount +
                                    " bills",
                                "error"
                            );
                        }
                    } else if (geometryType == "Polygon") {
                        // if polygen
                        var content = "";
                        for (var key in properties) {
                            if (key !== "geometry") {
                                content +=
                                    "<li><strong>" +
                                    key +
                                    ":</strong> " +
                                    properties[key] +
                                    "</li>";
                            }
                        }
                        // document.getElementById("featurePropertiesList").innerHTML =                     content;

                        var gisId = properties["gisid"]; // Get the GIS ID from the clicked feature
                        let valueFound = false;
                        polygonDatas.forEach(function (item) {
                            if (item.gisid == gisId) {
                                console.log(
                                    "===================================="
                                );

                                console.log(
                                    "===================================="
                                );
                                document.getElementById("number_bill").value =
                                    item.number_bill || "";
                                document.getElementById("number_shop").value =
                                    item.number_shop || "";
                                document.getElementById("number_floor").value =
                                    item.number_floor || "";

                                document.getElementById("building_name").value =
                                    item.building_name || "";
                                document.getElementById(
                                    "building_usage"
                                ).value = item.building_usage || "";
                                document.getElementById(
                                    "construction_type"
                                ).value = item.construction_type || "";
                                document.getElementById("road_name").value =
                                    item.road_name || "";
                                document.getElementById("ugd").value =
                                    item.ugd || "";
                                document.getElementById(
                                    "rainwater_harvesting"
                                ).value = item.rainwater_harvesting || "";
                                document.getElementById("parking").value =
                                    item.parking || "";
                                document.getElementById("ramp").value =
                                    item.ramp || "";
                                document.getElementById("hoarding").value =
                                    item.hoarding || "";
                                document.getElementById("building_type").value =
                                    item.building_type || "";
                                document.getElementById("basement").value =
                                    item.basement || "";
                                document.getElementById("liftroom").value =
                                    item.liftroom || "";
                                document.getElementById("overhead_tank").value =
                                    item.overhead_tank || "";
                                document.getElementById("headroom").value =
                                    item.headroom || "";
                                document.getElementById("cell_tower").value =
                                    item.cell_tower || "";
                                document.getElementById("percentage").value =
                                    item.percentage || "";
                                document.getElementById("new_address").value =
                                    item.new_address || "";
                                document.getElementById("cctv").value =
                                    item.solar_panel || "";

                                document.getElementById(
                                    "water_connection"
                                ).value = item.water_connection || "";
                                document.getElementById("phone").value =
                                    item.phone || "";
                                document.getElementById("remarks").value =
                                    item.remarks || "";

                                var imagel = gisId + ".png";
                                var basePath =
                                    "{{ asset('public/corporation/coimbatore') }}";

                                // Construct the full image path
                                var imagePath =
                                    basePath +
                                    "/" +
                                    data.zone +
                                    "/" +
                                    data.ward +
                                    "/images/" +
                                    image;

                                // Set the image path
                                $("#building_img").attr("src", imagePath);

                                console.log(imagePath);
                                valueFound = true;

                                // Break out of the loop since we found the value
                                return false;
                            }
                        });

                        if (!valueFound) {
                            console.log(data);
                            console.log("====================================");
                            console.log("no");
                            console.log("====================================");
                            // If the GIS ID is not present in polygonDatas, reset all input fields to empty
                            document.getElementById("number_bill").value = "";
                            document.getElementById("number_shop").value = "";
                            document.getElementById("number_floor").value = "";

                            document.getElementById("building_name").value = "";
                            document.getElementById("building_usage").value =
                                "";
                            document.getElementById("construction_type").value =
                                "";
                            // document.getElementById('road_name').value = "";
                            document.getElementById("ugd").value = "";
                            document.getElementById(
                                "rainwater_harvesting"
                            ).value = "";
                            document.getElementById("parking").value = "";
                            document.getElementById("ramp").value = "";
                            document.getElementById("hoarding").value = "";
                            document.getElementById("liftroom").value = "";
                            document.getElementById("overhead_tank").value = "";
                            document.getElementById("headroom").value = "";
                            document.getElementById("cell_tower").value = "";
                            document.getElementById("percentage").value = "";
                            document.getElementById("cctv").value = "";
                            document.getElementById("new_address").value = "";
                            document.getElementById("solar_panel").value = "";
                            document.getElementById("water_connection").value =
                                "";
                            document.getElementById("phone").value = "";
                            document.getElementById("image").value = "";
                            document.getElementById("remarks").value = "";
                            $("#building_img").attr("src", "");
                        }

                        // Set the GIS ID value in the form
                        document.getElementById("gisIdInput").value = gisId;
                        $("#buildingModal").modal("show");
                    } else if (
                        geometryType == "LineString" ||
                        geometryType == "MultiLineString"
                    ) {
                        // if line
                        var content = "";
                        for (var key in properties) {
                            // alert( key + ':</strong> ' + properties[key]);
                            if (key !== "geometry") {
                                content +=
                                    "<li><strong>" +
                                    key +
                                    ":</strong> " +
                                    properties[key] +
                                    "</li>";
                            }
                        }
                        document.getElementById("featureline").innerHTML =
                            content;
                        $("#linegisid").val(properties[gisid]);
                        $("#lineModal").modal("show");
                    }
                }
            }
        });
    });
    $("#buildingForm").submit(function (e) {
        e.preventDefault();
        $(".error-message").text("");
        $("input").removeClass("is-invalid");
        $("select").removeClass("is-invalid");

        // Disable submit button to prevent multiple submissions
        $("#buildingsubmitBtn").prop("disabled", true);

        var formData = new FormData(this);
        $.ajax({
            type: "POST",
            url: routes.surveyorPolygonDatasUpload,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    showFlashMessage(response.message, "success");
                    polygonDatas = response.polygonDatas;
                    polygons = response.polygon;
                    points = response.point;
                    refreshLayer(response.point, lines, response.polygon);
                }
                // Re-enable the submit button after success
                $("#buildingsubmitBtn").prop("disabled", false);
            },
            error: function (xhr, status, error) {
                console.log(error);
                let errorMsg =
                    "An error occurred while processing your request. Please try again.";

                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                }

                showFlashMessage(errorMsg, "error");

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key + "_error").text(value[0]);
                    });
                }

                // Re-enable the submit button in case of an error
                $("#buildingsubmitBtn").prop("disabled", false);
            },
            complete: function () {
                // Always re-enable the submit button after request completes
                $("#buildingsubmitBtn").prop("disabled", false);
            },
        });
    });

    // point form submit
    $("#pointForm").submit(function (e) {
        e.preventDefault();
        $(".error-message").text("");
        $("input").removeClass("is-invalid");

        var pointDatas = $(this).serialize();
        $("#pointSubmit").prop("disabled", true);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: routes.surveyorPointDataUpload,
            data: pointDatas,
            success: function (response) {
                showFlashMessage(response.message, "success");
                $(".added").remove();
                pointDatas = response.pointDatas;
                $("#surveycount").text(response.pointCount);
                refreshLayer(response.points, lines, polygons);
                $("#pointSubmit").prop("disabled", false);
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                let errorMsg =
                    "An error occurred while processing your request. Please try again.";
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                }
                showFlashMessage(errorMsg, "error");
                $("#pointSubmit").prop("disabled", false);
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key + "_error").text(value[0]);
                    });
                }
            },
            complete: function () {
                $("#pointSubmit").prop("disabled", false);
            },
        });
    });

    $("#assessment").keyup(function () {
        var inputValue = $(this).val();
        var matchingData = mis.filter(function (row) {
            return row.assessment === inputValue;
        });

        if (matchingData.length > 0) {
            $("#old_assessment").val(matchingData[0].old_assessment);
            $("#owner_name").val(matchingData[0].owner_name);
            $("#old_door_no").val(matchingData[0].old_door_no);
            $("#water_tax").val(matchingData[0].water_tax);
            $("#new_door_no").val(matchingData[0].OLD_door_no);
            $("#road_name").val(matchingData[0].road_name);
            $("#phone").val(matchingData[0].phone);
        }
    });
    $("#old_assessment").keyup(function () {
        var inputValue = $(this).val();
        var matchingData = mis.filter(function (row) {
            return row.old_assessment === inputValue;
        });

        if (matchingData.length > 0) {
            $("#assessment").val(matchingData[0].assessment);
            $("#owner_name").val(matchingData[0].owner_name);
            $("#old_door_no").val(matchingData[0].old_door_no);
            $("#water_tax").val(matchingData[0].water_tax);
            $("#new_door_no").val(matchingData[0].OLD_door_no);
            $("#road_name").val(matchingData[0].road_name);
            $("#phone").val(matchingData[0].phone);
        }
    });

    $("#addedFeature").change(function () {
        const value = $(this).val();

        // Helper function to remove draw interactions
        function removeDrawInteractions() {
            map.getInteractions().forEach((interaction) => {
                if (interaction instanceof ol.interaction.Draw) {
                    map.removeInteraction(interaction);
                }
            });
        }

        // Hide all forms initially
        $("#mergeForm, #delForm").addClass("d-none");

        if (value === "Polygon") {
            removeDrawInteractions();

            // Add a new draw interaction for polygons
            const draw = new ol.interaction.Draw({
                source: new ol.source.Vector(),
                type: "Polygon",
            });
            map.addInteraction(draw);

            draw.on("drawend", function (event) {
                const coordinates = event.feature
                    .getGeometry()
                    .getCoordinates();
                console.log(coordinates);

                $.ajax({
                    url: routes.addPolygonFeature,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    data: {
                        type: value,
                        coordinates: coordinates,
                    },
                    success: function (response) {
                        // Handle success response
                        points = response.points;
                        polygons = response.polygons;
                        refreshLayer(response.points, lines, response.polygons);
                        showFlashMessage(response.message, "success");
                        removeDrawInteractions();
                        $("#addedFeature").val("none");
                    },
                    error: function (xhr) {
                        // Handle error response
                        if (xhr.status === 401) {
                            // Show the error message from the response
                            const errorMessage =
                                xhr.responseJSON?.error ||
                                "An unknown error occurred.";
                            showFlashMessage(errorMessage, "error");
                            removeDrawInteractions();
                        } else {
                            // Handle other types of errors (if needed)
                            showFlashMessage(
                                "An error occurred. Please try again later.",
                                "error"
                            );
                            removeDrawInteractions();
                        }
                    },
                });
            });
        } else if (value === "Merge") {
            // Remove any draw interactions
            removeDrawInteractions();
            $("#mergeForm").removeClass("d-none");
        } else if (value === "Delete") {
            $("#delForm").removeClass("d-none");
        } else {
            removeDrawInteractions();
        }
    });
    $("#mergeForm").submit(function (e) {
        e.preventDefault();
        // Get values from form inputs
        var firstmerge = $("#firstmerge").val();
        var secondmerge = $("#secondmerge").val();
        // Send AJAX request to merge polygons
        if (firstmerge != secondmerge) {
            $.ajax({
                url: routes.mergePolygon,
                method: "POST", // Corrected to a string value 'POST'
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: {
                    firstmerge: firstmerge,
                    secondmerge: secondmerge,
                },
                success: function (response) {
                    // alert("Polygon and point merge successfully");
                    // console.log("Merge successful:", response);
                    if ($("#addedFeature").val() !== "none") {
                        $("#addedFeature").val("none");
                    }
                    showFlashMessage(response.message, "success");
                    $("#mergeForm").addClass("d-none");
                    refreshLayer(response.points, lines, response.polygons);
                    // Optionally handle success response
                },
                error: function (xhr, status, error) {
                    if (xhr.status === 401) {
                        // Show the error message from the response
                        const errorMessage =
                            xhr.responseJSON?.error ||
                            "An unknown error occurred.";
                        showFlashMessage(errorMessage, "error");
                        removeDrawInteractions();
                    } else {
                        // Handle other types of errors (if needed)
                        showFlashMessage(
                            "An error occurred. Please try again later.",
                            "error"
                        );
                        removeDrawInteractions();
                    }
                    $("#addedFeature").val("none");
                },
            });
        } else {
            showFlashMessage("two are equal", "success");
        }
    });
    $("#delForm").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        // Get value from form input
        var delete_gis_id = $("#delete_gis_id").val();

        if (delete_gis_id === "") {
            showFlashMessage("GIS ID cannot be empty.", "error");
            return;
        }

        if (
            confirm(
                "Are you sure you want to delete this GIS ID? " + delete_gis_id
            )
        ) {
            $.ajax({
                url: routes.deletePolygon,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: {
                    gisid: delete_gis_id, // Corrected the variable name
                },
                success: function (response) {
                    if (response.message) {
                        showFlashMessage(response.message, "success"); // Use actual message
                    } else {
                        showFlashMessage(
                            "Polygon deleted successfully.",
                            "success"
                        );
                    }
                    $("#delForm").addClass("d-none"); // Hide the form after successful deletion
                    $("#addedFeature").val("none");
                    refreshLayer(response.points, lines, response.polygons);
                },
                error: function (xhr, status, error) {
                    if (xhr.status === 401) {
                        const errorMessage =
                            xhr.responseJSON?.error ||
                            "Surveyor not authenticated.";
                        showFlashMessage(errorMessage, "error");
                    } else if (xhr.status === 404) {
                        const errorMessage =
                            xhr.responseJSON?.error || "Data not found.";
                        showFlashMessage(errorMessage, "error");
                    } else {
                        showFlashMessage(
                            "An error occurred. Please try again later.",
                            "error"
                        );
                    }
                    $("#addedFeature").val("none");
                },
            });
        }
    });
    $("#lineForm").on("submit", function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Gather form data
        var formData = new FormData(this);

        // AJAX request
        $.ajax({
            url: routes.updateRoadName, // Replace with your route
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                $("#lineSubmit").prop("disabled", true).text("Submitting...");
            },
            success: function (response) {
                // Handle success response
                alert("Form submitted successfully");
                $("#lineForm")[0].reset(); // Reset form
            },
            error: function (xhr, status, error) {
                // Handle error response
                alert("An error occurred: " + xhr.responseText);
            },
            complete: function () {
                $("#pointSubmit").prop("disabled", false).text("Submit");
            },
        });
    });
});
