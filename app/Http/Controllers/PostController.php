<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function search($term){
        $posts = Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;

    }
    

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

    public function showSinglePost(Post $post){
        //strip tags and allow only the folloing html tags
        $post['body'] = strip_tags(Str::markdown($post->body),'<p><ul><ol><li><strong><em><h3><br>');
        return view('single-post', ['post'=> $post]);
    }

    public function delete(Post $post){
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', "Deleted Post Successfully");
    }

    public function showEditForm(Post $post){
        return view('edit-post',['post' => $post]);
    }

    public function updatePost(Post $post, Request $request){
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $post->update($incomingFields);
        return back()->with('success', 'Updated successfully');

    }
}
