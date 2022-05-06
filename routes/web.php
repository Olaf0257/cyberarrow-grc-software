<?php

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

    Route::namespace('Auth')->group(function () {
        // laraguard 2fa routes
        Route::get('2fa-required', 'mfa\LoginEnableMFAController@showEnableMFAPage')->name('2fa.notice');
        Route::get('2fa-setup', 'mfa\LoginEnableMFAController@showSetupMFAPage')->name('2fa.setup');

        Route::get('/', 'LoginController@showLoginForm')->name('homepage');
        Route::get('/login', 'LoginController@showLoginForm')->name('login');
        Route::post('/login', 'LoginController@login');
        Route::get('/forget-password', [
            'uses' => 'ForgotPasswordController@forgetPasswordForm',
            'as' => 'forget-password',
        ]);

        Route::post('/forget-password', [
            'uses' => 'ForgotPasswordController@sendResetLinkEmail',
            'as' => 'forget-password-post',
        ]);

        Route::get('/reset/{token}', [
            'uses' => 'ResetPasswordController@showResetForm',
            'as' => 'reset-form',
        ]);

        // verify email to activate account
        Route::get('/admin/verify-email/{token}/set-pasword', 'VerificationController@verifyEmailAndSetPasswordShowForm')->name('admin-verity-email-set-password');
        Route::post('/admin/verify-email/{token}/set-pasword', 'VerificationController@verifyEmailAndSetPassword');

        Route::post('/reset', [
            'uses' => 'ResetPasswordController@reset',
            'as' => 'admin-reset-password',
        ]);

        Route::get('/forget-password/verify/email', [
            'uses' => 'ForgotPasswordController@forgetPasswordVerifyEmail',
            'as' => 'admin-forget-password-verify-email',
        ]);

        Route::get('/logout', 'LoginController@logout')->name('admin-logout');

        // 2 factor authentication

        Route::group(['prefix' => 'mfa'], function () {
            Route::namespace('mfa')->group(function () {
                /* Protected routes */
                Route::middleware(['auth:admin'])->group(function () {
                    Route::get('/setup-mfa', 'MultiFactorAuthenticationController@prepareTwoFactor')->name('setup-mfa');
                    Route::post('/validate-mfa-code', 'MultiFactorAuthenticationController@validateMfaCode')->name('validate-mfa-code');
                    Route::post('/confirm-mfa', 'MultiFactorAuthenticationController@confirmTwoFactor')->name('confirm-mfa');
                    Route::post('/reset-mfa', 'MultiFactorAuthenticationController@resetTwoFactorAuth')
                        ->name('reset-mfa');
                });

                /* Public routes */
                Route::post('/send-mfa-reset-link', 'MultiFactorAuthenticationController@sendMFAResetLink')->name('send-mfa-reset-link');
                Route::get('/mfa-reset-link/{token}', 'MultiFactorAuthenticationController@ResetMFA')->name('mfa-reset-link');
            });
        });
    });

    //API to get SAML Configuration for LoginPage
    Route::get('/getSAMLConfiguration', 'SessionController@getSAMLConfiguration')->name('getSAMLConfiguration');

    // This will be the route that checks expiration!
    Route::post('session/ajaxCheck', ['uses' => 'SessionController@ajaxCheck', 'as' => 'session.ajax.check']);

    Route::any('pages-lock-screen', 'SessionController@showPagesLockScreen')->name('pages-lock-screen')->middleware('guest:admin');

    // POLICY MANAGEMENT PUBLIC ROUTE
    include 'modules/policy-management/public.php';

    /******
     *  Below routes are for authenticated users
     *  `session timout middleware` log the user last activity and shows the session expiration message
     *  `session timout middleware` It is used for session timeout set in global settings
     *  `mfa_required ` when required redirect user to enable 2fa when alredy not set
     */

    Route::middleware(['auth:admin', 'session_timeout', 'mfa_required', '2fa.confirm'])->group(function () {
        // GLOBAL DASHBOARD
        include 'modules/global-dashboard/index.php';

        // RISK MANAGEMENT ROUTES
        include 'modules/risk-management.php';
        // RISK MANAGEMENT ROUTES ENDS HERE

        // COMPLIANCE ROUTES
        include 'modules/compliance.php';
        // COMPLIANCE ROUTES ENDS HERE

        // POLICY MODULE ROUTES
        include 'modules/policy-management/protected.php';

        // global settings
        include 'modules/administration/index.php';

        // data scope
        include 'modules/data-scope/index.php';

        //3rd party risk
        include 'modules/third-party-risk.php';

        //asset management
        include 'modules/asset-management.php';

        //KPI Dashboard
        include 'modules/kpi.php';
    }); // End of Middleware group

    Route::middleware(['auth:admin', 'session_timeout', 'mfa_required', '2fa.confirm','role:Global Admin|Compliance Administrator|Contributor|Risk Administrator'])->group(function () {
        Route::group(['prefix' => 'common'], function () {
            Route::name('common.')->group(function () {
                Route::get('/department-filter-tree-view-data', 'Administration\OrganizationManagement\DepartManagementController@departmentFilterTreeViewData')->name('department-filter-tree-view-data');
                Route::get('/get-all-department-filter-tree-view-data', 'Administration\OrganizationManagement\DepartManagementController@getAllDepartmentFilterTreeViewData')->name('get-all-department-filter-tree-view-data');
                Route::get('/department-users', 'Tasks\TaskReactController@getUsersByDepartment')->name('get-users-by-department');
                Route::get('/common/contributors', 'Tasks\TaskReactController@getContributorsList')->name('contributors');
                Route::get('/get-all-projects', 'Tasks\TaskReactController@getAllProjects')->name('get-all-projects');
                Route::get('/get-all-projects-without-datascope', 'Tasks\TaskReactController@getAllProjectFilterDataWithoutDataScope')->name('get-all-projects-without-datascope');
            });
        });
    });

    // Route for third party risk email's
    Route::namespace('ThirdPartyRisk')->prefix('third-party-risk')->name('third-party-risk.')->group(function () {
        Route::get('take-questionnaire/{token}/', 'QuestionnaireAnswerController@show')->name('take-questionnaire');
        Route::post('take-questionnaire', 'QuestionnaireAnswerController@store')->name('save-questionnaire');
    });
