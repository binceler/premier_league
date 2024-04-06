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

Route::get('/', "App\Http\Controllers\HomeController@getLeague")->name('home.get_league');
Route::get('/play-all', "App\Http\Controllers\HomeController@play")->name('home.play');
Route::get('/play-weekly/{week}', "App\Http\Controllers\HomeController@playWeekly")->name('home.play_weekly');
Route::get('/reset', "App\Http\Controllers\HomeController@reset");
Route::get('fixture', "App\Http\Controllers\HomeController@refreshFixture");

