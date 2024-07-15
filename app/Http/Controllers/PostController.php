<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function showCreatePost(){
        return view('create-post');

    }

    public function createPost(Request $request){

        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        //create the field in the array and the user id with help of global auth().
        $incomingFields['user_id'] = auth()->id();
        $post = Post::create($incomingFields);
        return redirect("/post/{$post->id}")->with('success', 'New post created successfully');

    }

    public function showSinglePost(Post $postId){
       
        return view('single-post', ['post'=> $postId]);
    }
}
