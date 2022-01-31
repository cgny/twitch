<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::group([ 'middleware' => ['auth'], 'namespace' => 'App\Http\Controllers' ], function ()
{
    Route::get('/dashboard', function () {

        Route::get('/', 'HomeController@index')->name('home.index');
    })->name('dashboard');


    Route::prefix('home')->group(function ()
    {
        Route::get('/', 'HomeController@index')->name('home.index');
    });

    //API Auth
    Route::prefix('auth')->group(function ()
    {
        Route::get('/authorize', 'OAuthController@authorizeAccess')->name('auth.authorize');
        Route::get('/access', 'OAuthController@access')->name('auth.access');
    });

    Route::prefix('twitch')->group(function ()
    {
        Route::get('/streams', 'TwitchController@streams')->name('twitch.streams');
        Route::get('/stats', 'TwitchController@stats')->name('twitch.stats');
        Route::get('/loadStreams', 'TwitchController@loadStreams')->name('twitch.load_streams');
        Route::post('/follow', 'TwitchController@follow')->name('twitch.follow');
        Route::post('/unfollow', 'TwitchController@unfollow')->name('twitch.unfollow');
    });

});

require __DIR__.'/auth.php';
