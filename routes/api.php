<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//
Route::post('register', [UserController::class, 'register']);
//follow
Route::post('follow', [UserController::class, 'follow']);
//Post
Route::post('social_store', [PostController::class, 'social_store']);
Route::post('social_comment', [PostController::class, 'social_comment']);
Route::post('comment_edit', [PostController::class, 'social_comment_edit']);
Route::get('postview', [PostController::class, 'postview']);
Route::get('userview', [PostController::class, 'userview']);
Route::post('post_like', [PostController::class, 'postlike']);
Route::post('post_edit', [PostController::class, 'post_edit']);
Route::post('post_delete', [PostController::class, 'post_delete']);
//story
Route::post('social_story', [StoryController::class, 'social_story']);
Route::get('story_view', [StoryController::class,'story_view']);
Route::get('user_story_view', [StoryController::class,'user_story_view']);
Route::post('story_delete', [StoryController::class, 'story_delete']);
