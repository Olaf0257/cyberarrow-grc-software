<?php

use Illuminate\Support\Facades\Route;


Route::namespace('License')->group(function () {
    Route::prefix('license')->group(function () {
        Route::name('license')->group(function () {
            $license_controller = 'LicenseController';
            Route::get('/activate', $license_controller.'@activationPage')->name('.activate');
            Route::get('/license-contact-support', $license_controller.'@contactSupport')->name('.contact.support');
            Route::post('/activate', $license_controller.'@activateLicense');
            Route::get('/check-for-updates', $license_controller.'@checkForUpdates')->name('.check.update');
            Route::post('/download-updates', $license_controller.'@downloadUpdate')->name('.download.update');
            Route::get('/get-license-details', $license_controller.'@get_license')->name('.details');
        });
    });
});
