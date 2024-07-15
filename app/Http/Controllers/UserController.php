<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    public function login(Request $request){
        $incomingFields = $request->validate([
            'loginusername'=> 'required',
            'loginpassword'=> 'required'
        ]);
       

        if(auth()->attempt(['username'=>$incomingFields['loginusername'],'password'=>$incomingFields['loginpassword']]))
                {
                    $request->session()->regenerate();
                    return redirect('/')->with('success', 'You have logged in successfully.');
                } else {
                    return redirect('/')->with('failure', 'Invalid Credentials.');
                };

    }

    public function register(Request $request){
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:15', Rule::unique('users','username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'confirmed']

        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $newUser =  User::create($incomingFields);
        auth()->login($newUser);
        return redirect('/')->with('register', 'Thank you for creating an account.'); 
    }

    public function showCorrectHomepage(){
        if(auth()->check()){
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function logout(){
        auth()->logout();
        return redirect('/')->with('logout','You have logged out successfully.');
    }

    public function showProfile(User $user){

        // $userPosts =  $user->matchPosts()->get();

        return view('profile-posts',[
         'username'=> $user->username,
         'posts'=> $user->matchPosts()->latest()->get(),
         'postCount'=> $user->matchPosts()->count()]);
    }
}
