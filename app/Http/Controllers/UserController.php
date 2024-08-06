<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
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


    private function getSharedData($user){
        $following = 0;
        if(auth()->check()){
            $following = Follow::where([['user_id','=' ,auth()->user()->id],['followeduser', '=', $user->id]])->count();
                
        }

        View::share('sharedData',[ 
            'username'=> $user->username,
            'postCount'=> $user->matchPosts()->count(),
            'avatar'=> $user->avatar,
            'following'=> $following,
            'totalFollowing'=>$user->following()->count(),
            'totalFollowers'=>$user->followers()->count()]);
    }   

    public function showProfile(User $user){
        // $userPosts =  $user->matchPosts()->get();
       $this->getSharedData($user);

        return view('profile-posts',['posts'=> $user->matchPosts()->latest()->get()]);
    }

    public function showProfileFollowers(User $user){
        // $userPosts =  $user->matchPosts()->get();
        $this->getSharedData($user);
        return view('profile-followers',['followers'=> $user->followers()->latest()->get()]);

    }


    public function showProfileFollowing(User $user){
        // $userPosts =  $user->matchPosts()->get();
        
        $this->getSharedData($user);
        return view('profile-following',['following'=> $user->following()->latest()->get()]);

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
