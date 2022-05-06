<?php

Route::prefix('data-scope')->group(function () {
    Route::name('data-scope')->group(function () {
        Route::namespace('DataScope')->group(function () {
            Route::name('data-scope')->group(function () {
                Route::get('/get-tree-view-data', 'DataScopeController@getTreeViewDropdownData')->name('.get-tree-view-data');
            });
        });
    });
});


Route::namespace('DataScope')->group(function () {
    Route::get('/get-auth-user-info', 'DataScopeController@getAuthUserDetails')->name('get-auth-user-info');
});



