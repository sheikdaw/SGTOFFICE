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
                            <div class="card-footer bg-light d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.area.variation', ['id' => $data['id']]) }}"
                                    class="btn btn-primary btn-sm area-variation" data-id="{{ $data['id'] }}">
                                    Area Variation
                                </a>


                                <a href="{{ route('admin.usage.variation', ['id' => $data['id']]) }}"
                                    class="btn btn-secondary btn-sm usage-variation" data-id="{{ $data['id'] }}">Usage
                                    Variation</a>
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
    <script>
        const routes = {
            datastore: "{{ route('admin.datastore') }}",

        };
    </script>
@endsection
