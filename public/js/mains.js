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
});
