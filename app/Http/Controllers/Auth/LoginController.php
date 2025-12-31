<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function login(Request $request)
    {
        $this->validateLogin($request);
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
    protected function attemptLogin(Request $request)
    {
        // dd($request);
        // Attempt to log in the user
        $credentials = $this->credentials($request);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Check if the user is active
            if ($this->isUserActive($request)) {
                $user = auth()->user();
                $logData['user_id'] = $user->id;
                $logData['resolution_id'] = 0;
                $logData['action'] = "User '{$user->name}' (ID: {$user->id}) has logged in.";
                addUserAction($logData);
                return true; // User is active, proceed with login
            } else {
                Auth::logout(); // Log out the user if not active
                return false; // User is not active, prevent login
            }
        }
    }

    protected function isUserActive(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is active (adjust the condition based on your column name)
        return ($user && ($user->is_active == 1 || $user->type == "0"));
    }
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function logout(Request $request)
    {
        $logData = [];
        $user = auth()->user();
        $logData['user_id'] = $user->id;
        $logData['resolution_id'] = 0;
        $logData['action'] = "User '{$user->name}' (ID: {$user->id}) has logged out.";
        addUserAction($logData);
        // Custom code before logout
        // Example: Log an event, perform cleanup, etc.

        Auth::logout();

        // Custom code after logout
        // Example: Redirect to a different page, show a message, etc.

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
