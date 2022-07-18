<?php

use Illuminate\Support\Facades\Route;

use GomaGaming\Logs\Http\Controllers\GomaGamingApiController;

Route::get('/log-services', [GomaGamingApiController::class, 'getLogServices']);

Route::group(['prefix' => 'logs'], function () {

    Route::get('/', [GomaGamingApiController::class, 'getLogs']);
    Route::get('/{logId}', [GomaGamingApiController::class, 'getLog']);

});

Route::group(['prefix' => 'exceptions'], function () {

    Route::get('/', [GomaGamingApiController::class, 'getLogExceptions']);
    Route::get('/{logExceptionId}', [GomaGamingApiController::class, 'getLogException']);
    Route::get('/{logExceptionId}/logs', [GomaGamingApiController::class, 'getLogsByException']);

    Route::post('/{logExceptionId}/assign', [GomaGamingApiController::class, 'postLogExceptionAssignee']);
    Route::post('/archive', [GomaGamingApiController::class, 'postLogExceptionArchive']);

});
