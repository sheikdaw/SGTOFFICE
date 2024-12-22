$(document).ready(function () {
    // Flash Message Display Function

    // Optional: Console log to see the points data

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
                            console.log("====================================");

                            console.log("====================================");
                            document.getElementById("number_bill").value =
                                item.number_bill || "";
                            document.getElementById("number_shop").value =
                                item.number_shop || "";
                            document.getElementById("number_floor").value =
                                item.number_floor || "";

                            document.getElementById("building_name").value =
                                item.building_name || "";
                            document.getElementById("building_usage").value =
                                item.building_usage || "";
                            document.getElementById("construction_type").value =
                                item.construction_type || "";
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

                            document.getElementById("water_connection").value =
                                item.water_connection || "";
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
                        document.getElementById("building_usage").value = "";
                        document.getElementById("construction_type").value = "";
                        // document.getElementById('road_name').value = "";
                        document.getElementById("ugd").value = "";
                        document.getElementById("rainwater_harvesting").value =
                            "";
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
                        document.getElementById("water_connection").value = "";
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
                    var gisid = properties["gisid"];
                    $("#pointgis").val(gisid);
                    console.log("Line feature properties:", properties);

                    if (gisid) {
                        console.log("Retrieved GIS ID:", gisid);
                        $("#linegisid").val(gisid);
                    } else {
                        console.error(
                            "GIS ID not found for the selected line."
                        );
                    }

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
                    document.getElementById("featureline").innerHTML = content;
                    $("#lineModal").modal("show");
                }
            }
        }
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
                // / polygons = response.polygon;
                points = response.points;

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
        } else if (value === "Line") {
            removeDrawInteractions(); // Remove existing interactions

            // Add a new draw interaction for LineString
            var draw = new ol.interaction.Draw({
                source: new ol.source.Vector(),
                type: "LineString",
            });
            map.addInteraction(draw); // Add the interaction to the map

            draw.on("drawend", function (event) {
                // Get the coordinates of the drawn line
                const coordinates = event.feature
                    .getGeometry()
                    .getCoordinates();
                console.log(coordinates);

                // Send the line coordinates to the server using AJAX
                $.ajax({
                    url: routes.addLineFeature, // Ensure this route is defined in your backend
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ), // CSRF token for security
                    },
                    data: {
                        type: value, // Type of feature (LineString)
                        coordinates: coordinates, // The drawn line coordinates
                    },
                    success: function (response) {
                        // Handle success response from the server
                        points = response.points;
                        polygons = response.polygons;
                        lines = response.lines;
                        refreshLayer(
                            response.points,
                            response.lines,
                            response.polygons
                        );
                        showFlashMessage(response.message, "success"); // Display success message
                        removeDrawInteractions(); // Remove the drawing interaction after success
                        $("#addedFeature").val("none"); // Reset the feature type field
                    },
                    error: function (xhr) {
                        // Handle error response from the server
                        if (xhr.status === 401) {
                            const errorMessage =
                                xhr.responseJSON?.error ||
                                "Unauthorized access.";
                            showFlashMessage(errorMessage, "error"); // Display error message
                        } else {
                            showFlashMessage(
                                "An error occurred. Please try again later.",
                                "error"
                            ); // General error message
                        }
                        removeDrawInteractions(); // Remove the drawing interaction in case of error
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
                showFlashMessage(response.message, "success");
                lines = response.lines;
                refreshLayer(points, response.lines, polygons);

                $("#lineForm")[0].reset();
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
