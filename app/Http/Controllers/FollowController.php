<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(User $user){

        if($user->id === auth()->user()->id){
            return back()->with('failure', 'Cannot follow your self');
        }

        //can follow same user twice
        $existCheck = Follow::where([['user_id', '=', auth()->user()->id],['followeduser', '=', $user->id]])->count();
        if($existCheck){
            return back()->with('failure', "You already follow {$user->username}");
        }

        $newFollow = new Follow();
        $newFollow->user_id  =  auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        return back()->with('Success',' You now follow this user');
    }

    public function unfollow(Request $request){
        
    }
}
