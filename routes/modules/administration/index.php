<?php

// global settings
// ONLY FOR GLOBAL ADMIN
Route::middleware(['role:Global Admin'])->group(function () {
    Route::namespace('Administration')->group(function () {
        Route::prefix('global-settings')->group(function () {
            Route::name('global-settings')->group(function () {
                Route::post('/store', 'GlobalSettingsController@store')->name('.store');
                Route::post('/mail-settings', 'GlobalSettingsController@updateMailSetting')->name('.mail-settings');
                Route::get('/test-mail-connection', 'GlobalSettingsController@testMailConnection')->name('.test-mail-connection');

                Route::get('/test-ldap-connection', 'GlobalSettingsController@testLdapConnection')->name('.test-ldap-connection');
                Route::post('/ldap-settings', 'GlobalSettingsController@updateLdapSetting')->name('.ldap-settings');

                // saml settings
                Route::prefix('saml-settings')->group(function () {
                    Route::name('.saml-settings')->group(function () {
                        Route::post('/', 'GlobalSettingsController@updateSamlSetting');
                        Route::get('/download/sp-metadata', 'GlobalSettingsController@downloadSpMetadata')->name('.download.sp-metadata');
                        Route::post('/saml-provider-metadata/upload', 'GlobalSettingsController@uploadSamlProviderMetadata')->name('.saml-provider-metadata.upload');
                        Route::post('/saml-provider-metadata/import', 'GlobalSettingsController@importSamlProviderMetadata')->name('.saml-provider-metadata.import');
                        Route::post('/saml-provider-metadata/remove', 'GlobalSettingsController@removeSamlSettings')->name('.saml-provider-metadata.remove');
                    });
                });

                // Organization settings
                Route::namespace('OrganizationManagement')->group(function () {
                    Route::prefix('organizations')->group(function () {
                        Route::name('.organizations')->group(function () {
                            Route::post('/store', 'OrganizationManagementController@store')->name('.store');
                            Route::post('/store/{id}', 'OrganizationManagementController@update')->name('.update');

                            Route::prefix('{id}/departments')->group(function () {
                                Route::name('.departments')->group(function () {
                                    Route::post('/store', 'DepartManagementController@store')->name('.store');
                                    Route::post('/save-nested-departments', 'DepartManagementController@saveNestedDepartment')->name('.save-nested-departments');
                                    Route::get('{department}/edit', 'DepartManagementController@edit')->name('.edit');
                                    Route::post('{department}/update', 'DepartManagementController@update')->name('.update');
                                    Route::get('{department}/delete', 'DepartManagementController@delete')->name('.delete');
                                    Route::get('{department}/department-transferable-user', 'DepartManagementController@getTransferableDepartments')->name('.department-transferable-user');
                                    Route::post('{department}/department-transferable', 'DepartManagementController@userDepartmentTransfer')->name('.department-transferable');
                                    Route::get('{department}/department-transferable-user-count', 'DepartManagementController@getDepartmentCount')->name('.department-transferable-user-count');

                                    Route::get('/', 'DepartManagementController@index');
                                });
                            });
                        });
                    });
                });


                /* Risk Settings*/
                Route::namespace('RiskSettings')->group(function () {
                    Route::prefix('risk-matrix')->group(function () {
                        Route::name('.risk-matrix')->group(function () {
                            Route::post('/update', 'RiskScoreMatrixController@update')->name('.update');
                            Route::post('/restore-to-default', 'RiskScoreMatrixController@restoreRiskMatrixToDefault')->name('.restore-to-default');
                        });
                    });
                });

                Route::get('/', 'GlobalSettingsController@index');
            });
        });
    });

    Route::namespace('Integration')->group(function () {
        Route::group(['prefix' => 'integrations'], function () {
            Route::get('/', 'IntegrationController@index')->name('integrations.index');
        }); // route prefix group ends here
    });
});

Route::namespace('Administration')->group(function () {
    Route::group(['prefix' => 'users'], function () {
        // user profile edit
        Route::get('/edit/{admin}', [
            'uses' => 'UserManagementController@edit',
            'as' => 'admin-user-management-edit',
        ]);
        // user profile Update
        Route::post('/store/{admin}', [
            'uses' => 'UserManagementController@update',
            'as' => 'admin-user-management-update',
        ]);

        // ONLY FOR GLOBAL ADMIN
        Route::middleware(['role:Global Admin'])->group(function () {

            Route::get('/view', [
                'uses' => 'UserManagementController@view',
                'as' => 'admin-user-management-view',
            ]);

            Route::get('/create', [
                'uses' => 'UserManagementController@create',
                'as' => 'admin-user-management-create',
            ]);

            Route::get('/get-ldap-user-info', 'UserManagementController@getLdapUserInfo')->name('get-ldap-user-info');

            Route::post('/store', [
                'uses' => 'UserManagementController@store',
                'as' => 'admin-user-management-store',
            ]);

            Route::delete('{admin}/delete', [
                'uses' => 'UserManagementController@delete',
                'as' => 'admin-user-management-delete',
            ]);

            Route::get('{admin}/make-active', [
                'uses' => 'UserManagementController@makeActive',
                'as' => 'admin-user-management-make-active',
            ]);

            Route::get('{admin}/make-disable', [
                'uses' => 'UserManagementController@makeDisable',
                'as' => 'admin-user-management-make-disable',
            ]);

            Route::get('/disable/{user}', [
                'uses' => 'UserManagementController@disableUser',
                'as' => 'admin-user-management-disable-user',
            ]);
            Route::get('/activate/{user}', [
                'uses' => 'UserManagementController@activateUser',
                'as' => 'admin-user-management-activate-user',
            ]);

            Route::get('/get-json-data', [
                'uses' => 'UserManagementController@getJsonData',
                'as' => 'admin-user-management-get-json-data',
            ]);

            Route::get('/get-user-data-react', [
                'uses' => 'UserManagementController@getUsersDataReact',
                'as' => 'admin-user-management-get-user-data-react',
            ]);

            Route::get('{admin}/resend-email-verification-link', 'UserManagementController@resendEmailVerificationLink')->name('users.resend-email-verification-link');

            // Transfer user responsibility
            Route::get('{admin}/assignments-transferable-users', 'UserManagementController@getAssignmentTransferableUsers')->name('user.assignments-transferable-users');
            Route::get('{admin}/project-assignments', 'UserManagementController@getUserProjectAssignments')->name('user.project-assignments');
            Route::post('{admin}/transfer-assignments', 'UserManagementController@transferAssignments')->name('user.transfer-assignments');
        }); // middleware group ends here
    }); // route prefix group ends here

    Route::post('users/{admin}/update-password', 'UserManagementController@updatePassword')->name('admins.update-password');
});
