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

            // Utility function to create table headers
            function createTableHeaders(headers) {
                headers.forEach(function(header) {
                    if (header !== 'created_at' && header !== 'updated_at') {
                        $("<th>").text(header).appendTo("#tableHeaders");
                    }
                });
                $("<th>").text("Action").appendTo("#tableHeaders");
            }

            // Utility function to create table rows
            function createTableRow(item, headers) {
                var row = $("<tr id='row-" + item.id + "'>");

                headers.forEach(function(header) {
                    if (header !== 'created_at' && header !== 'updated_at') {
                        var readOnly = (header === 'corporation_id' || header === 'id') ? 'readonly' : '';
                        $("<td>").html("<input type='text' value='" + item[header] +
                            "' name='" + header + "' " + readOnly + ">").appendTo(row);
                    }
                });

                // Add Action buttons
                $("<td>").html("<button type='button' class='btn btn-success updateBtn'>Update</button>").appendTo(
                    row);
                $("<td>").html("<button type='button' class='btn btn-danger deleteBtn'>Delete</button>").appendTo(
                    row);

                $("#tableBody").append(row);
            }

            // Populate table dynamically
            function populateTable(data) {
                if (data.length > 0) {
                    var headers = Object.keys(data[0]);
                    createTableHeaders(headers);

                    data.forEach(function(item) {
                        createTableRow(item, headers);
                    });
                } else {
                    $("#tableBody").html(
                        "<tr><td colspan='5' class='text-center form-control'>No data found</td></tr>");
                }
            }

            // Populate table on load
            populateTable(response);

            // Update button handler
            $(document).on("click", ".updateBtn", function() {
                var row = $(this).closest("tr");
                var rowData = {};
                row.find("input").each(function() {
                    rowData[$(this).attr("name")] = $(this).val();
                });

                var rowId = row.attr("id").replace("row-", "");
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "{{ route('admin.updateAssessment') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {

                        data: rowData
                    },
                    success: function(response) {
                        alert("Update successful");
                    },
                    error: function(xhr) {
                        alert("Update error: " + xhr.responseJSON?.message || "Unknown error");
                    }
                });
            });

            // Delete button handler
            $(document).on("click", ".deleteBtn", function() {
                var row = $(this).closest("tr");
                var rowId = row.attr("id").replace("row-", "");
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "{{ route('admin.deleteAssessment') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        id: rowId
                    },
                    success: function(response) {
                        row.remove();
                        alert("Delete successful");
                    },
                    error: function(xhr) {
                        alert("Delete error: " + xhr.responseJSON?.message || "Unknown error");
                    }
                });
            });
        });
    </script>
@endsection
