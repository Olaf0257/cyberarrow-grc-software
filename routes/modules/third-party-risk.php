<?php

use Illuminate\Support\Facades\Route;

Route::namespace('ThirdPartyRisk')->prefix('third-party-risk')->name('third-party-risk.')->middleware('role:Global Admin|Third Party Risk Administrator')->group(function () {
    Route::post('questionnaires/{questionnaire}/questions/batch-import', 'QuestionController@batchImport')->name('questionnaires.questions.batch-import');
    Route::get('questionnaires/questions/download-sample', 'QuestionController@downloadSample')->name('questionnaires.questions.download-sample');
    Route::get('questionnaires/get-json-data', 'QuestionnaireController@getJsonData')->name('questionnaires.get-json-data');
    Route::get('questionnaires/{questionnaire}/duplicate', 'QuestionnaireController@duplicateIndex')->name('questionnaires.duplicate.index');
    Route::post('questionnaires/{questionnaire}/duplicate', 'QuestionnaireController@duplicateStore')->name('questionnaires.duplicate.store');
    Route::get('questionnaires/{questionnaire}/questions/get-json-data', 'QuestionController@getJsonData')->name('questionnaires.questions.get-json-data');

    Route::resource('questionnaires', QuestionnaireController::class);
    Route::resource('questionnaires.questions', QuestionController::class);

    // Questions
    Route::resource('questions', QuestionController::class);


    // Vendors
    Route::get('vendors/get-json-data', 'VendorController@getJsonData')->name('vendors.get-json-data');
    Route::resource('vendors', VendorController::class);
    Route::resource('vendors', VendorController::class)->except(['show', 'create']);

    // Dashboard
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('dashboard/get-vendors-data', 'DashboardController@getVendorsData')->name('dashboard.get-vendors-data');
    Route::get('dashboard/get-top-vendors', 'DashboardController@getTopVendors')->name('dashboard.get-top-vendors');
    Route::get('dashboard/export-pdf', 'DashboardController@exportPDF')->name('dashboard.export-pdf');

    // Projects
    Route::get('projects/options', 'ProjectController@options')->name('projects.options');
    Route::get('projects/get-json-data', 'ProjectController@getJsonData')->name('projects.get-json-data');
    Route::get('projects/{project}/answers', 'ProjectController@answers')->name('projects.get-project-answers');
    Route::get('projects/{project}/export-csv', 'ProjectController@exportCSV')->name('projects.export-csv');
    Route::post('projects/{id}/reminder', 'ProjectController@sendReminder')->name('projects.send-project-reminder');
    Route::get('projects/{id}/export-pdf', 'ProjectController@exportPDF')->name('projects.export-pdf');
    Route::resource('projects', ProjectController::class)->except(['create']);
});
