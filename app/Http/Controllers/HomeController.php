<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        
        return view('home', compact('user'));
    }

    public function editProfile(Request $request){
        $user = User::find($request->user_id);
        
        if(empty($request->password)){
            $this->validate($request,[
                'name' => 'required|max:255',
                'email' => 'required|email',
            ]);

            $photo = $user->photo;

            if($request->photo){
                $this->validate($request,['photo' => 'image|mimes:jpg,jpeg,png']);
                $photo = $request->file('photo')->store('image_users');
                $photo_path = $user->photo;
                if(Storage::exists($photo_path)){
                    Storage::delete($photo_path);
                }
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'photo' => $photo
            ]);
        }else{
            $this->validate($request,[
                'name' => 'required|max:255',
                'email' => 'required|email',
                'password' => 'same:password_confirmation|min:6'
            ]);

            $photo = $user->photo;

            if($request->photo){
                $this->validate($request,['photo' => 'image|mimes:jpg,jpeg,png']);
                $photo = $request->file('photo')->store('image_users');
                $photo_path = $user->photo;
                if(Storage::exists($photo_path)){
                    Storage::delete($photo_path);
                }
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'photo' => $photo
            ]);
        }
        return redirect()->route('home')->with('success', 'Update profile is successfully');
    }
}
