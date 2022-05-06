<?php


Route::middleware('role:Global Admin|Compliance Administrator')->group(function () {
    Route::prefix('global')->group(function () {
        Route::name('global')->group(function () {
            Route::namespace('Globals')->group(function () {
                Route::get('/dashboard', 'DashboardController@showDashboard')->name('.dashboard');
                Route::post('/dashboard/generate-report', 'DashboardController@generatePdfReport')->name('.dashboard.generate-report');
                Route::get('/dashboard/get-data', 'DashboardController@getDashboardData')->name('.dashboard.get-data');
                Route::get('/dashboard/get-calendar-data', 'DashboardController@getcalendartask')->name('.dashboard.get-caledar-data');
                Route::get('/dashboard/get-calendar-more-popover-data', 'DashboardController@getCalendarMorePopoverData')->name('.dashboard.get-calendar-more-popover-data');
                Route::get('/project-filter-data', 'DashboardController@projectFilterData')->name('.project-filter-data');
            });

            // Tasks monitor routes
            /* User Tasks For All Modules */
            Route::group(['prefix' => 'tasks', 'middleware' => ['role:Global Admin|Compliance Administrator|Contributor']], function () {
                Route::namespace('Tasks')->group(function () {

                    // React Routes
                    // Route::get('/get-page-data', 'TaskReactController@getPageData')->name('.tasks.get-page-data');
                    Route::get('/get-my-task-json-data', 'TaskReactController@myTasksJsonData')->name('.tasks.get-my-tasks-json-data')->middleware('data_scope');

                    Route::get('/get-projects-by-scope', 'TaskReactController@getProjects')->name('.tasks.get-projects-by-scope')->middleware('data_scope');

                    //End React Routes

                    Route::get('/', 'TaskReactController@index')->name('.tasks');

                    Route::get('/all-active', 'TaskReactController@index')->name('.tasks.all-active');

                    Route::get('/due-today', 'TaskReactController@index')->name('.tasks.due-today');

                    Route::get('/pass-due', 'TaskReactController@index')->name('.tasks.pass-today');

                    Route::get('/under-review', 'TaskReactController@index')->name('.tasks.under-review');

                    Route::get('/need-my-approval', 'TaskReactController@index')->name('.tasks.need-my-approval');

                    Route::get('/my-tasks-json-data', 'TaskController@myTasksJsonData')->name('.tasks.my-tasks-json-data');

                    Route::get('/get-projects-by-standards', 'TaskController@getProjectByStandards')->name('.tasks.get-projects-by-standards');

                    Route::get('/get-project-control-by-projects', 'TaskController@getProjectControlByProjects')->name('.tasks.get-project-control-by-projects');

                    Route::get('/export-data', 'TaskController@exportData')->name('.tasks.export-data');
                });
            });
        });
    });
});
