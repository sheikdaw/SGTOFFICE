<?php

namespace App\Http\Controllers;

use App\Mail\forgetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Surveyor;
use App\Models\CBE;
use App\Models\TaxCollector;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected $guards = ['admin', 'surveyor', 'cbe', 'taxcollector'];

    // Show login form
    public function showLoginForm()
    {
        // $this->validateRules = [
        //     "name" => "required",
        //     "phone" => "required|unique:users,phone",
        //     "gender" => "required|in:1,2,3",
        //     "password" => "required",
        //     "re_password" => "required:same:password",
        // ];

        // $apiValidate = $this->apiValidate($request->all());

        // // validations with errors
        // if($apiValidate) {
        //     return $this->apiResponse($apiValidate, 400);
        // }

        foreach ($this->guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect()->route($guard . '.dashboard');
            }
        }
        return view('Auth.login');
    }

    // Handle login submission
    public function submitLogin(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Loop through each guard to attempt authentication
        foreach ($this->guards as $guard) {
            // Attempt to log the user in with the given credentials
            if (Auth::guard($guard)->attempt($request->only('email', 'password'))) {
                // Authentication passed, return a JSON response with redirect URL
                return response()->json(['redirect' => route($guard . '.dashboard')]);
            }
        }

        // If authentication failed, return an unauthorized response
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function logout(Request $request)
    {
        // Log out the current user for the active guard
        Auth::guard()->logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token to prevent session fixation
        $request->session()->regenerateToken();

        // Redirect to login page or another page
        return redirect('/login');
    }
    public function forgetPassword()
    {
        return view('Auth.forget');
    }
    public function forgetEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check the user types
        $surveyor = Surveyor::where('email', $request->email)->first();
        $cbe = CBE::where('email', $request->email)->first();
        $taxCollector = TaxCollector::where('email', $request->email)->first();

        // If any user type is found, proceed with sending the reset email
        if ($surveyor) {
            // $mailData = [
            //     'title' => 'Mail from ItSolutionStuff.com',
            //     'body' => 'This is for testing email using SMTP.'
            // ];
            // Mail::to($surveyor->email)->send(new forgetMail($mailData)); // Updated this line
            return $this->sendResetEmail($surveyor);
        } elseif ($cbe) {
            return $this->sendResetEmail($cbe);
        } elseif ($taxCollector) {
            return $this->sendResetEmail($taxCollector);
        }

        // Return error if no user found
        return response()->json(['error' => 'Email not found'], 404);
    }


    private function sendResetEmail($user)
    {
        // Generate a password reset token
        $user->password_reset_token = Str::random(6);
        $user->save();

        // Send email with the reset token
        $resetLink = url('/password/reset/' . $user->password_reset_token);
        $subject = "Password Reset Request";
        $message = "Click here to reset your password: " . $resetLink;

        // Use mail() to send the email (or replace with a more robust mailer)
        mail($user->email, $subject, $message);

        // Return success response
        return response()->json(['message' => 'Email sent']);
    }
}
