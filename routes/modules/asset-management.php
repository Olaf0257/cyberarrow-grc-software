<?php
use Inertia\Inertia;

Route::middleware(['role:Global Admin'])->group(function () {
    Route::namespace('AssetManagement')->group(function () {

        Route::group(['prefix' => 'asset-management'], function () {
            Route::name('asset-management.')->group(function () {
                Route::get('/', function (){
                   return Inertia::render('asset-management/AssetManagement');
                })->name('index');
            });
        });
    });
});