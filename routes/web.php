<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticateSpotifyController;
use App\Http\Controllers\LibrarySpotifyController;
use App\Http\Controllers\YoutubeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([App\Http\Middleware\SpotifyAuthenticate::class])->group(function () {
    Route::get('/', [LibrarySpotifyController::class, 'view']);
    Route::get('/tracks', [LibrarySpotifyController::class, 'getTracksJson']);
});


Route::get('/login/spotify', [AuthenticateSpotifyController::class, 'spotifyLogin'])->name('login');
Route::get('/callback', [AuthenticateSpotifyController::class, 'spotifyCallback']);
Route::get('/getVideoIdByName', [YoutubeController::class, 'getVideoIdByName']);
Route::get('/getVideosByName', [YoutubeController::class, 'getVideosByName']);

Route::get('/welcome', function () {
    return view('welcome');
});
