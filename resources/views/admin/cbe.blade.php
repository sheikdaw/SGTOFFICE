@extends('layout.main-layout')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12 text-end mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCorporationModal">
                Add Corporation
            </button>
        </div>

        @if ($corporations->isEmpty())
            <div class="col-12">
                <div class="alert alert-warning text-center" role="alert">
                    No data found.
                </div>
            </div>
        @else
        <div id="corporationContainer" class="row">
            @foreach ($corporations as $corporation)
            <div class="col-md-4 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">{{ $corporation->name }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-4">
                            <li>
                                <i class="bi bi-envelope"></i>
                                <strong>Email:</strong> {{ $corporation->email }}
                            </li>
                            <li>
                                <i class="bi bi-key"></i>
                                <strong>Password:</strong> {{ $corporation->password }}
                            </li>
                        </ul>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary cbeUpdate"
                                data-id="{{ $corporation->id }}"
                                data-name="{{ $corporation->name }}"
                                data-email="{{ $corporation->email }}"
                                data-password="{{ $corporation->password }}">
                                <i class="bi bi-pencil-square"></i> Update
                            </button>
                            <button class="cbeDelete btn btn-danger" data-id="{{ $corporation->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Add Corporation Modal -->
<div class="modal fade" id="addCorporationModal" tabindex="-1" aria-labelledby="addCorporationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="storeCorporation" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addCorporationModalLabel">Add New Corporation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control">
                        <div class="invalid-feedback" id="name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                        <div class="invalid-feedback" id="email_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                        <div class="invalid-feedback" id="password_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Corporation Modal -->
<div class="modal fade" id="updateCorporationModal" tabindex="-1" aria-labelledby="updateCorporationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateCorporation" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="updateCorporationModalLabel">Update Corporation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="update_name" class="form-label">Name</label>
                        <input type="text" name="name" id="update_name" class="form-control">
                        <div class="invalid-feedback" id="update_name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="update_email" class="form-label">Email</label>
                        <input type="email" name="email" id="update_email" class="form-control">
                        <div class="invalid-feedback" id="update_email_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="update_password" class="form-label">Password</label>
                        <input type="password" name="password" id="update_password" class="form-control">
                        <div class="invalid-feedback" id="update_password_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const routes = {
        cbeStore: "{{ route('admin.cbeStore') }}",
        cbeUpdate: "{{ route('admin.cbeUpdate') }}",
        cbeDelete: "{{ route('admin.cbeDelete', ['id' => 'mm']) }}", // Use a placeholder
    };
</script>


@endsection
