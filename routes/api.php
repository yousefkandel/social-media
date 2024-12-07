<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\TweetController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    // Tweets routes
    Route::post('/Tweet', [TweetController::class, 'store']);
    Route::post('/tweets/{id}', [TweetController::class, 'update']);
    Route::post('/tweets/{tweetId}/like', [TweetController::class, 'likeTweet']);
    Route::post('/tweets/{tweetId}/comment', [TweetController::class, 'commentOnTweet']);
    Route::get('/tweets/following', [TweetController::class, 'showTweets']);

    // Follow/Unfollow routes
    Route::post('/follow/{userId}', [FollowController::class, 'follow']);
    Route::post('/unfollow/{userId}', [FollowController::class, 'unfollow']);

    // User info route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
