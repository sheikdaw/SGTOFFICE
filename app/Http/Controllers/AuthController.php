<?php

namespace App\Http\Controllers;

use App\Mail\forgetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Surveyor;
use App\Models\CBE;
use App\Models\TaxCollector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        // Validate the email input
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if email exists in any user type
        $user = Surveyor::where('email', $request->email)->first() ??
            CBE::where('email', $request->email)->first() ??
            TaxCollector::where('email', $request->email)->first();

        if ($user) {
            return $this->sendResetEmail($user);
        }

        // Return error if no user found
        return response()->json(['error' => 'Email not found'], 404);
    }

    private function sendResetEmail($user)
    {
        // Generate a password reset token
        $user->password_reset_token = Str::random(40); // Generates a secure 40-character token
        $user->save();

        // Prepare email data
        $resetLink = url('/password/reset/' . $user->password_reset_token);
        $subject = "Password Reset Request";
        $message = "Click here to reset your password: " . $resetLink;

        // Send email using Laravel's Mail facade
        Mail::raw($message, function ($mail) use ($user, $subject) {
            $mail->to($user->email)
                ->subject($subject);
        });

        // Return success response
        return response()->json(['message' => 'Reset email sent successfully.']);
    }
    public function showResetForm($token)
    {
        return view('auth.reset', ['token' => $token]);
    }

    // Handle the password reset
    public function resetPassword(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if the user exists in any of the user types
        $user = Surveyor::where('email', $request->email)
            ->where('password_reset_token', $request->token)
            ->first() ??
            CBE::where('email', $request->email)
            ->where('password_reset_token', $request->token)
            ->first() ??
            TaxCollector::where('email', $request->email)
            ->where('password_reset_token', $request->token)
            ->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Invalid email or token'])->withInput();
        }

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->password_reset_token = null; // Clear the reset token
        $user->save();

        // Redirect with success message
        return redirect()->route('login')->with('status', 'Password has been reset successfully!');
    }
}
