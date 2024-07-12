<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function login(){

    }

    public function register(Request $request){
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required',
            'email' => 'required'

        ]);

        User::create($incomingFields);
        
        return $request;
    }
}
