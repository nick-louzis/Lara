<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/admin', function(){
    if(Gate::allows('visitAdminPanel')){
        return "Welcome Admin";
    }
    return 'Only for admin';
});


//User related routes
Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login');
Route::post('/register', [UserController::class, "register"])->middleware('guest');
Route::post('/login', [UserController::class, "login"])->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])->middleware('mustBeLoggenIn');

//Post related routes
Route::get('/create-post', [PostController::class, "showCreatePost"])->middleware('mustBeLoggenIn');
Route::post('/create-post', [PostController::class, "createPost"])->middleware('mustBeLoggenIn');
Route::get('/post/{post}', [PostController::class, "showSinglePost"]);
//way to use policy authorization with midleware
Route::delete('/post/{post}', [PostController::class, "delete"])->middleware('can:delete,post');

Route::get('/post/{post}/edit', [PostController::class, "showEditForm"])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, "updatePost"])->middleware('can:update,post');
//profile routes

//search based on username
Route::get('/profile/{user:username}', [UserController::class, "showProfile"]);
Route::get('/manage-avatar', [UserController::class, "showAvatarForm"])->middleware('mustBeLoggenIn');
Route::post('/manage-avatar', [UserController::class, "saveAvatar"])->middleware('mustBeLoggenIn');


Route::post('/create-follow/{user:username}',[ FollowController::class,'follow']);
Route::post('/remove-follow/{user:username}',[ FollowController::class,'unfollow']);