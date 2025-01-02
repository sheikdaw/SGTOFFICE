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
            // Ensure pointData and data_id are correctly passed to JS
            var response = @json($pointData); // Ensure this data is correctly passed from the controller
            var data_id = @json($data_id);
            console.log(response);

            $("#tableHeaders").empty();
            $("#tableBody").empty();

            if (response.length > 0) {
                var headers = Object.keys(response[0]);

                // Dynamically create headers
                headers.forEach(function(header) {
                    $("<th>").text(header).appendTo("#tableHeaders");
                });
                $("<th>").text("Action").appendTo("#tableHeaders");

                // Loop through the response to create table rows
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

                    // Add hidden data_id field
                    $("<td>").html("<input type='hidden' value='" + data_id + "' name='data_id' " +
                        readOnly + ">").appendTo(row);

                    // Add action buttons for update and delete
                    $("<td>").html(
                            "<button type='button' class='btn btn-success updateBtn'>Update</button>")
                        .appendTo(row);
                    $("<td>").html("<button type='button' class='btn btn-danger deleteBtn'>Delete</button>")
                        .appendTo(row);

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
                    url: "{{ route('admin.updateAssessment') }}",
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
                    url: "{{ route('admin.deleteAssessment') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        id: rowId,
                        data_id: data_id
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
