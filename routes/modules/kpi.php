<?php
use Inertia\Inertia;

Route::middleware(['role:Global Admin'])->group(function () {
    Route::namespace('KPI')->group(function () {
            Route::name('kpi.')->group(function () {
                Route::get('/kpi-dashboard', function (){
                    return Inertia::render('controls/Dashboard');
                })->name('index');
            });
    });
});