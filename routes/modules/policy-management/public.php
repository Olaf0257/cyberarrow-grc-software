<?php

Route::namespace('PolicyManagement')->group(function () {
    Route::prefix('policy-management')->group(function () {
        Route::name('policy-management')->group(function () {
            /***
            *  Campaigns routes
            */
            Route::namespace('Campaign')->group(function () {
                Route::prefix('campaigns')->group(function () {
                    Route::name('.campaigns')->group(function () {
                        Route::prefix('acknowledgement')->group(function () {
                            Route::name('.acknowledgement')->group(function () {
                                Route::get('/{token}/show', 'AcknowledgementController@show')->name('.show');
                                Route::post('/confirm', 'AcknowledgementController@confirm')->name('.confirm');
                                Route::get('/completed', 'AcknowledgementController@showCompletedPage')->name('.completed');
                                Route::get('/new_policy_url', 'AcknowledgementController@getNewUrlForS3Policy')->name('.new_policy_url');
                            });
                        });
                    });
                });
            });
        });
    });
});
