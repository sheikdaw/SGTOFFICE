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
                <form id="loginForm" class="mb-3"> <!-- Corrected ID here -->
                    @csrf <!-- CSRF token for security -->
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div id="email_error" class="text-danger"></div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div id="password_error" class="text-danger"></div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>

                <a href="{{ route('forget-password') }}">ForgetPassword</a>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $('#email_error').text('');
                $('#password_error').text('');
                $('#errorMessage').hide();

                $.ajax({
                    url: "{{ route('submitLogin') }}", // Update this to your login route
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {

                        // Handle success, maybe redirect to a dashboard or home page
                        window.location.href = response
                            .redirect; // Assuming your backend sends a redirect URL
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.email) {
                                $('#email_error').text(errors.email[0]);
                            }
                            if (errors.password) {
                                $('#password_error').text(errors.password[0]);
                            }
                        } else {
                            // Handle general error
                            $('#errorMessage').text(
                                'An unexpected error occurred. Please try again.').show();
                        }
                    }
                });
            });
        });
    </script>
@endsection
