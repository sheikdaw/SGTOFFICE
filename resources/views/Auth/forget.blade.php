@extends('layout.auth-layout')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card" style="width: 500px;">
        <div class="card-header text-center">
            <h4>Forget Password</h4>
        </div>
        <div class="card-body">
            <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
            <form id="ForgetEmailForm" class="mb-3">
                @csrf <!-- CSRF token for security -->
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    <div id="email_error" class="text-danger"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#ForgetEmailForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Clear previous error messages
            $('#email_error').text('');
            $('#errorMessage').hide();

            $.ajax({
                url: "{{ route('forget-Email') }}", // Update this to your route for forgetEmail
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    alert("An email has been sent with instructions to reset your password.");
                    // You can redirect the user or handle success
                },
                error: function(xhr) {
                    // Handle validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.email) {
                            $('#email_error').text(errors.email[0]);
                        }
                    } else {
                        // Handle general error
                        $('#errorMessage').text('Email not Found').show();
                    }
                }
            });
        });
    });
</script>
@endsection
