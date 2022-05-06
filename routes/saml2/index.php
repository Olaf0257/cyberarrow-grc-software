<?php

Route::namespace('Auth\Saml2')->group(function () {
    Route::prefix('saml2')->group(function () {
        Route::name('saml2')->group(function () {
            $saml2_controller = 'Saml2Controller';

            Route::get('/login', $saml2_controller.'@login')->name('.login');

            Route::get('/metadata', $saml2_controller.'@getMetadata')->name('.metadata');

            Route::post('/acs', $saml2_controller.'@acs')->name('.acs');

            Route::get('/sls', $saml2_controller.'@sls')->name('.sls');
            
            Route::post('/sls', $saml2_controller.'@sls');

            Route::get('/logout',  $saml2_controller.'@logout')->name('.logout');
        });
    });
});