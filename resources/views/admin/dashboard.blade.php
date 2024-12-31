@extends('layout.main-layout')
@section('content')
    <div class="container">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dataStore">
            Add Data
        </button>
        <div class="container mt-4">
            <h2 class="text-center mb-4 text-primary">Data Overview</h2>
            <div class="row">
                <!-- Dynamic Data Cards -->
                @foreach ($datas as $data)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-lg border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">Ward: {{ $data['ward'] }}</h5>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Zone: {{ $data['zone'] }}</h6>
                                <p class="card-text">
                                    <strong>Corporation:</strong> {{ $data['corporation'] }}<br>
                                    <strong>ID:</strong> {{ $data['id'] }}<br>
                                    <strong>MIS Count:</strong> {{ $data['miscount'] }}<br>
                                    <strong>Surveyed Data Count:</strong> {{ $data['pointdatacount'] }}<br>
                                    <strong>Connected Data Count:</strong> {{ $data['connected'] }}<br>
                                    <strong>Not Connected Data Count:</strong>
                                    {{ $data['pointdatacount'] - $data['connected'] }}
                                </p>
                            </div>
                            <select name="road_name" class="form-control" id="road_name">
                                <option value="">Select a road</option>
                                @foreach ($data['road_name'] as $road)
                                    <option value="{{ $road }}">{{ $road }}</option>
                                @endforeach
                            </select>

                            <div class="card-footer bg-light d-flex flex-wrap gap-2">
                                <button {{-- href="{{ route('admin.area.variation', ['id' => $data['id']]) }}" --}} class="btn btn-primary btn-sm area-variation"
                                    data-id="{{ $data['id'] }}">
                                    Area Variation
                                </button>
                                <button {{-- href="{{ route('admin.usage.variation', ['id' => $data['id']]) }}" --}} class="btn btn-secondary btn-sm usage-variation"
                                    data-id="{{ $data['id'] }}">Usage
                                    Variation</button>
                                <a href="{{ route('admin.usageandarea.variation', ['id' => $data['id']]) }}"
                                    class="btn btn-success btn-sm usage-and-area-variation"
                                    data-id="{{ $data['id'] }}">Usage and Area Variation</a>
                                <a href="#" class="btn btn-warning btn-sm final-format"
                                    data-id="{{ $data['id'] }}">Final Format</a>
                                <a href="{{ route('admin.downloadPolygons', ['id' => $data['id']]) }}"
                                    class="btn btn-info btn-sm download-polygon" data-id="{{ $data['id'] }}">Download
                                    Polygon</a>
                                <a href="{{ route('admin.downloadPoints', ['id' => $data['id']]) }}"
                                    class="btn btn-light btn-sm download-point" data-id="{{ $data['id'] }}">Download
                                    Point</a>
                                <a href="{{ route('admin.downloadLines', ['id' => $data['id']]) }}"
                                    class="btn btn-dark btn-sm download-lines" data-id="{{ $data['id'] }}">Download
                                    Lines</a>
                                <a href="#" class="btn btn-danger btn-sm surveyor-count"
                                    data-id="{{ $data['id'] }}">Surveyor Count</a>
                                <a href="{{ route('admin.downloadsteetwise', ['id' => $data['id']]) }}"
                                    class="btn btn-primary btn-sm surveyor-count" data-id="{{ $data['id'] }}">Street
                                    Wise</a>
                                <button {{-- href="{{ route('admin.downloadMissingBill', ['id' => $data['id']]) }}" --}} class="btn btn-primary btn-sm missing-bill"
                                    data-id="{{ $data['id'] }}">
                                    Missing bill
                                </button>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>






        <!-- Modal -->
        <div class="modal fade" id="dataStore" tabindex="-1" aria-labelledby="dataStoreLabel" aria-hidden="true">

            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="dataStoreLabel">Data Store </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="dataStoreform">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label">CORPORATION:</label>
                                <select name="corporation" id="corporation" class="form-control">
                                    <option value="">Select Corporation</option>
                                    @foreach ($corporations as $corporation)
                                        <option value="{{ $corporation->id }}">{{ $corporation->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="corporation_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">ZONE:</label>
                                <input type="text" class="form-control" id="zone" name="zone">
                                <div class="invalid-feedback" id="zone_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">WARD:</label>
                                <input type="text" class="form-control" id="ward" name="ward">
                                <div class="invalid-feedback" id="ward_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">IMAGE:</label>
                                <input type="file" class="form-control" id="image" name="image">
                                <div class="invalid-feedback" id="image_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">POINT:</label>
                                <input type="file" class="form-control" id="point" name="point">
                                <div class="invalid-feedback" id="point_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">LINE:</label>
                                <input type="file" class="form-control" id="line" name="line">
                                <div class="invalid-feedback" id="line_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">POLYGON:</label>
                                <input type="file" class="form-control" id="polygon" name="polygon">
                                <div class="invalid-feedback" id="polygon_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">EXTEND LEFT:</label>
                                <input type="text" class="form-control" id="extend-left" name="extend-left">
                                <div class="invalid-feedback" id="extend-left_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">EXTEND RIGHT:</label>
                                <input type="text" class="form-control" id="extend-right" name="extend-right">
                                <div class="invalid-feedback" id="extend-right_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">EXTEND TOP:</label>
                                <input type="text" class="form-control" id="extend-top" name="extend-top">
                                <div class="invalid-feedback" id="extend-top_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">EXTEND BOTTOM:</label>
                                <input type="text" class="form-control" id="extend-bottom" name="extend-bottom">
                                <div class="invalid-feedback" id="extend-bottom_error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">MIS:</label>
                                <input type="file" class="form-control" id="mis" name="mis">
                                <div class="invalid-feedback" id="mis_error"></div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        const routes = {
            datastore: "{{ route('admin.datastore') }}",

        };

        $(document).ready(function() {
            // Handling clicks for .area-variation button
            $(".area-variation").click(function() {
                var dataId = $(this).data("id"); // Get the data-id attribute
                var road_name = $("#road_name").val(); // Get the road name value

                var data = {
                    id: dataId,
                    road_name: road_name
                };

                console.log('Data Sent:', data); // Log the data being sent

                $.ajax({
                    url: {{ route('admin.area.variation') }}, // Laravel route helper will generate the correct URL
                    method: 'GET', // Or 'POST' if you're submitting sensitive data
                    data: data, // Send the data to the server
                    success: function(response) {
                        console.log('Response:', response); // Log the response from the server
                        // Do something with the response, like updating the UI
                        // Example: $('#response-container').html(response.message);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status + ", " + error); // Handle errors
                    }
                });
            });
            // Handling clicks for .variation button
            $(".usage-variation").click(function() {
                var dataId = $(this).data("id"); // Get the data-id attribute
                var road_name = $("#road_name").val(); // Get the road name value

                var data = {
                    id: dataId,
                    road_name: road_name
                };

                // Show alert for testing purposes
                alert("Variation - Data ID: " + dataId + " Road Name: " + road_name);

                // Perform the AJAX request for Variation
                $.ajax({
                    url: route('admin.variation'), // Adjust the route URL accordingly
                    method: 'GET', // Use POST if needed
                    data: data, // Send the data to the server
                    success: function(response) {
                        console.log(response); // Handle the server's response
                        // Optionally, update the page with the response data
                        // Example: $('#some-element').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status + ", " + error); // Handle errors
                    }
                });
            });

            // Handling clicks for .missing-bill button
            $(".missing-bill").click(function() {
                var dataId = $(this).data("id"); // Get the data-id attribute
                var road_name = $("#road_name").val(); // Get the road name value

                var data = {
                    id: dataId,
                    road_name: road_name
                };

                // Show alert for testing purposes
                alert("Missing Bill - Data ID: " + dataId + " Road Name: " + road_name);

                // Perform the AJAX request for Missing Bill
                $.ajax({
                    url: route('admin.missing.bill'), // Adjust the route URL accordingly
                    method: 'GET', // Use POST if needed
                    data: data, // Send the data to the server
                    success: function(response) {
                        console.log(response); // Handle the server's response
                        // Optionally, update the page with the response data
                        // Example: $('#some-element').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status + ", " + error); // Handle errors
                    }
                });
            });
        });
    </script>
@endsection
