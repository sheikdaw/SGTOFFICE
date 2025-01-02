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
            var response = @json($pointData);
            var surveyor = @json($surveyor);
            console.log(response);

            $("#tableHeaders").empty();
            $("#tableBody").empty();

            if (response.length > 0) {
                var headers = Object.keys(response[0]);

                headers.forEach(function(header) {
                    $("<th>").text(header).appendTo("#tableHeaders");
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


                    // Check if the surveyor's name matches the worker_name to allow update
                    if (item['worker_name'] === surveyor.name || surveyor.name === "sgt" || surveyor
                        .name === "sir" || surveyor.name === "Anand") {
                        $("<td>").html(
                                "<button type='button' class='btn btn-success updateBtn'>Update</button>")
                            .appendTo(row);
                    }


                    $("#tableBody").append(row);
                });
            } else {
                $("#tableBody").html(
                    "<tr><td colspan='5' class='text-center form-control'>No data found</td></tr>");
            }

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
                    url: "{{ route('surveyor.updateAssessment') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        id: rowId,
                        data: rowData
                    },
                    success: function(response) {

                        alert("update successfully");
                    },
                    error: function(xhr, status, error) {
                        alert("update error");
                    }
                });
            });
        });
    </script>
@endsection
