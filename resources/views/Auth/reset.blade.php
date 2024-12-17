@extends('layout.auth-layout')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card" style="width: 500px;">
            <div class="card-header text-center">
                <img src="{{ asset('img/ccmc.png') }}" alt="CCMC Logo" class="img-fluid"
                    style="max-width: 100%; height: auto; max-height: 150px;">
            </div>
            <div class="card-body">
                <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
                <form id="resetForm" class="mb-3">
                    @csrf <!-- CSRF token for security -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control">
                        <div id="email_error" class="text-danger"></div>
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                        <div id="password_error" class="text-danger"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        <div id="password_confirmation_error" class="text-danger"></div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#resetForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Clear previous error messages
                $('#email_error').text('');
                $('#password_error').text('');
                $('#password_confirmation_error').text('');
                $('#errorMessage').hide();

                $.ajax({
                    url: "{{ route('password.update') }}", // Backend route for password reset
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Handle success (redirect or show success message)
                        console(response); // Optionally, display a success message
                        window.location.href = "{{ route('login') }}"; // Redirect to login page
                    },
                    error: function(xhr) {
                        // Handle validation errors or general errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.email) {
                                $('#email_error').text(errors.email[0]);
                            }
                            if (errors.password) {
                                $('#password_error').text(errors.password[0]);
                            }
                            if (errors.password_confirmation) {
                                $('#password_confirmation_error').text(errors
                                    .password_confirmation[0]);
                            }
                        } else {
                            // Display general error message
                            $('#errorMessage').text(
                                'An unexpected error occurred. Please try again.'
                            ).show();
                        }
                    }
                });
            });
        });
    </script>
@endsection
