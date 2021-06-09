<?php

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

Route::get('/estrutura', function () {
    return view('estrutura-rest-api');
});

Route::get('/testexx', function () {
    // return view('welcome');
    echo $rr = env('DIR_FILES');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
