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

Route::post('signup',[
    'uses' => 'UserController@RegisterUser',
    'as'   => 'signup'
]);

Route::get('/home/{post}', [
    'uses' => 'UserController@getHome'
])->name('home.show');

Route::get('/callback', [
    'uses' => 'UserController@getCallback'
])->name('home.show');