<?php

use App\Http\Middleware\IpAccessHandler;

/*
  |--------------------------------------------------------------------------
  | Amin Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register admin routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
Route::middleware([
    'web'
    // IpAccessHandler::class
])->group(function () {

    Route::get('/',function(){
        return view('central.index');
    });
    Route::get('/register_domain','\App\Nova\Controller\RegisterDomain@showForm')->name('register.domain.show_form');

    Route::post('/register_domain','\App\Nova\Controller\RegisterDomain@submit')->name('register.domain.create');
});