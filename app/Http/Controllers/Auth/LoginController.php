<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Mail;
use Illuminate\Http\Request;
use Auth;
use App\User;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function authenticated(Request $request, $user){
        if (Auth::user()->confirmed == 0) {
            $user = User::where('email', $request->email)->first();
            
            $this->guard()->logout();
            $request->session()->invalidate();
            Mail::send("mails.confirmation", ['email' => $request->email, 'name' => $user->name, 'token' => $user->token], function($message) use($request){
                $message->to($request->email);
                $message->subject('Registration Confirmation');
            });
            return back()->with('danger', 'You need to confirm your account. We have sent you an activation link, please check your email.');
        }
        return redirect()->intended($this->redirectPath());
    }
}
