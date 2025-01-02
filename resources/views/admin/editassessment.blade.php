@extends('layout.main-layout')


@section('content')
    <div id="flash-message-container"></div>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-border table-primary align-middle" id="dataTable">
            <thead class="table-light">
                <caption>Edit GISID</caption>
                <tr id="tableHeaders"></tr>
            </thead>
            <tbody class="table-group-divider" id="tableBody">
                <!-- Rows will be dynamically generated here -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var response = @json($pointData); // Assuming $pointData is a Laravel variable
            var WARD = @json($data_id); // Global data_id for deletion, passed from the backend

            console.log(data_id); // Check if data_id is being passed correctly

            // Clear the table headers and body initially
            $("#tableHeaders").empty();
            $("#tableBody").empty();

            if (response.length > 0) {
                var headers = Object.keys(response[0]);

                // Dynamically create table headers, excluding 'created_at' and 'updated_at'
                headers.forEach(function(header) {
                    if (header !== 'created_at' && header !== 'updated_at') {
                        $("<th>").text(header).appendTo("#tableHeaders");
                    }
                });
                $("<th>").text("Action").appendTo("#tableHeaders");

                response.forEach(function(item) {
                    var row = $("<tr id='row-" + item.id + "'>");

                    headers.forEach(function(header) {
                        if (header !== 'created_at' && header !== 'updated_at') {
                            var readOnly = (header === 'corporation_id' || header === 'id') ?
                                'readonly' : '';
                            $("<td>").html("<input type='text' value='" + item[header] +
                                "' name='" + header + "' " + readOnly + ">").appendTo(row);
                        }
                    });

                    // Ensure data_id exists in item and assign it to the input field


                    // Add 'data_id' field (it should not be editable)
                    var dataIdTd = $("<td>").html("<input type='text' value='" + WARD +
                        "' name='data_id' readonly>");

                    console.log("Appending data_id input to row: ", dataIdTd); // Debug log for appending

                    row.append(dataIdTd); // Append the 'data_id' input field to the row

                    // Add Update button
                    $("<td>").html(
                            "<button type='button' class='btn btn-success updateBtn'>Update</button>")
                        .appendTo(row);

                    // Add Delete button
                    $("<td>").html("<button type='button' class='btn btn-danger deleteBtn'>Delete</button>")
                        .appendTo(row);

                    // Append the row to the table body
                    $("#tableBody").append(row);
                });

            } else {
                $("#tableBody").html(
                    "<tr><td colspan='5' class='text-center form-control'>No data found</td></tr>");
            }

            // Update button click handler
            $(document).on("click", ".updateBtn", function() {
                var row = $(this).closest("tr");
                var rowData = {};

                row.find("input").each(function() {
                    rowData[$(this).attr("name")] = $(this).val();
                });

                var rowId = row.attr("id").replace("row-", "");
                console.log("Input values for row with ID " + rowId + ":", rowData);

                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "{{ route('admin.updateAssessment') }}", // Ensure the route is correct
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        id: rowId,
                        data: rowData
                    },
                    success: function(response) {
                        alert("Update successful");
                    },
                    error: function(xhr, status, error) {
                        alert("Update error");
                    }
                });
            });

            // Delete button click handler
            $(document).on("click", ".deleteBtn", function() {
                var row = $(this).closest("tr");
                var rowId = row.attr("id").replace("row-", "");
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Send AJAX request to delete
                $.ajax({
                    url: "{{ route('admin.deleteAssessment') }}", // Ensure the route is correct
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        id: rowId,
                        data_id: data_id // This value should be passed to the backend for deletion
                    },
                    success: function(response) {
                        row.remove();
                        alert("Delete successful");
                    },
                    error: function(xhr, status, error) {
                        alert("Delete error");
                    }
                });
            });
        });
    </script>
@endsection
