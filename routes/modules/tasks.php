<?php

// User Tasks for all modules
Route::middleware(['data_scope'])->group(function () {
    Route::group(['prefix' => 'tasks', 'middleware' => ['role:Global Admin|Compliance Administrator|Contributor']], function () {
        Route::namespace('Tasks')->group(function () {
            Route::get('/', 'TaskReactController@index')->name('.tasks')->withoutMiddleware(['data_scope']);

            Route::get('/get-my-task-json-data', 'TaskReactController@myTasksJsonData')->name('.tasks.get-my-tasks-json-data');

            Route::get('/all-active', 'TaskReactController@index')->name('.tasks.all-active')->withoutMiddleware(['data_scope']);

            Route::get('/due-today', 'TaskReactController@index')->name('.tasks.due-today')->withoutMiddleware(['data_scope']);

            Route::get('/pass-due', 'TaskReactController@index')->name('.tasks.pass-today')->withoutMiddleware(['data_scope']);

            Route::get('/under-review', 'TaskReactController@index')->name('.tasks.under-review')->withoutMiddleware(['data_scope']);

            Route::get('/need-my-approval', 'TaskReactController@index')->name('.tasks.need-my-approval')->withoutMiddleware(['data_scope']);

            Route::get('/my-tasks-json-data', 'TaskController@myTasksJsonData')->name('.tasks.my-tasks-json-data');

            Route::get('/get-projects-by-standards', 'TaskController@getProjectByStandards')->name('.tasks.get-projects-by-standards');

            Route::get('/get-project-control-by-projects', 'TaskController@getProjectControlByProjects')->name('.tasks.get-project-control-by-projects');

            Route::post('/export-data', 'TaskController@exportData')->name('.tasks.export-data');
        });
    });
});
