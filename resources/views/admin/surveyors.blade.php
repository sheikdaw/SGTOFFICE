@extends('layout.main-layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 text-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSurveyorModal">
                    Add Surveyor
                </button>
            </div>
            @if ($surveyors->isEmpty())
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        No data found.
                    </div>
                </div>
            @else
                <div class="row show-surveyors">
                    @foreach ($surveyors as $surveyor)
                        <div class="col-md-4">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $surveyor->name }}</h5>
                                    <h5> {{ $surveyor->id }}</h5>
                                    <p class="card-text">
                                        <strong>Email:</strong> {{ $surveyor->email }} <br>
                                        <strong>Mobile:</strong> {{ $surveyor->mobile }} <br>
                                        <strong>Data ID:</strong> {{ $surveyor->data_id }} <br>
                                        <strong>Password:</strong> {{ $surveyor->password }} <br>
                                        <strong>Password Reset Token:</strong> {{ $surveyor->password_reset_token }}
                                    </p>
                                    <button class="btn btn-primary editSurveyor" data-name="{{ $surveyor->name }}"
                                        data-i="{{ $surveyor->id }}" data-email="{{ $surveyor->email }}"
                                        data-mobile="{{ $surveyor->mobile }}" data-password="{{ $surveyor->password }}"
                                        data-data_id="{{ $surveyor->data_id }}">Update</button>
                                    <button class="btn btn-danger delete-surveyor"
                                        data-id="{{ $surveyor->id }}">Delete</button>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Modal for Adding Surveyor -->
    <div class="modal fade" id="addSurveyorModal" tabindex="-1" aria-labelledby="addSurveyorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addSurveyorForm" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSurveyorModalLabel">Add New Surveyor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback">Please enter a name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" required>
                            <div class="invalid-feedback">Please enter a mobile number.</div>
                        </div>
                        <div class="mb-3">
                            <label for="data_id" class="form-label">Data ID</label>
                            <select class="form-control" id="data_id" name="data_id" required>
                                <option value="" selected>Select a Data ID</option> <!-- Default option -->
                                @foreach ($datas as $data)
                                    <option value="{{ $data->id }}">ward {{ $data->ward }}</option>
                                    <!-- Adjust based on your data model -->
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a data ID.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">Please enter a password.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Model for update surveyors --}}
    <div class="modal fade" id="updateSurveyorModal" tabindex="-1" aria-labelledby="updateSurveyorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="updateSurveyorForm" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateSurveyorModalLabel">Update Surveyor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="update_id" name="id">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="update_name" name="name" required>
                            <div class="invalid-feedback">Please enter a name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="update_email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input type="text" class="form-control" id="update_mobile" name="mobile" required>
                            <div class="invalid-feedback">Please enter a mobile number.</div>
                        </div>
                        <div class="mb-3">
                            <label for="data_id" class="form-label">Data ID</label>
                            <select class="form-control" id="update_data_id" name="data_id" required>
                                <option value="" selected>Select a Data ID</option> <!-- Default option -->
                                @foreach ($datas as $data)
                                    <option value="{{ $data->id }}">ward {{ $data->ward }}</option>
                                    <!-- Adjust based on your data model -->
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a data ID.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="update_password" name="password" required>
                            <div class="invalid-feedback">Please enter a password.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        const routes = {
            SurveyorStore: "{{ route('admin.store-Surveyor') }}",
            surveyorUpdate: "{{ route('admin.surveyorUpdate') }}",
            surveyorDelete: "{{ route('admin.surveyorDelete', ['id' => 'mm']) }}", // Use a placeholder
        };
    </script>


@endsection
