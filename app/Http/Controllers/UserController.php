<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;


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
        $following=0;
        if(auth()->check()){
            $following = Follow::where([['user_id',auth()->user()->id],['followeuser', '=', $user->id]]);

        }

        return view('profile-posts',[
         'username'=> $user->username,
         'posts'=> $user->matchPosts()->latest()->get(),
         'postCount'=> $user->matchPosts()->count(),
         'avatar'=> $user->avatar,
         'following'=> $following
        ]);
    }

    public function showAvatarForm(){
        return view('avatar-form');
    }

    public function saveAvatar(Request $request){ 
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);
        $user= auth()->user();
        $filename = $user->id . "-" . uniqid() . ".jpg";

        $manager = new ImageManager( new Driver());
        $image = $manager->read($request->file('avatar'));
        $imgData  = $image->cover(120,120)->toJpeg();
        Storage::put("public/avatars/" . $filename, $imgData);
        
        $oldAvatar =$user->avatar;
        

        $user->avatar = $filename;
        $user->save();

        if($oldAvatar != '/fallbackavatar.jpg'){
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with("success","You now have a new avatar!");
    }
}
