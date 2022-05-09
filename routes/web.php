<?php

use Illuminate\Support\Facades\Route;

use GomaGaming\Logs\Http\Controllers\GomaGamingApiController;

Route::group(['prefix' => 'exceptions'], function () {

    Route::get('/', [GomaGamingApiController::class, 'getLogExceptions']);
    Route::get('/{logExceptionId}', [GomaGamingApiController::class, 'getLogException']);
    Route::get('/{logExceptionId}/logs', [GomaGamingApiController::class, 'getLogsByException']);

    Route::post('/{logExceptionId}/assign', [GomaGamingApiController::class, 'postLogExceptionAssignee']);
    Route::post('/archive', [GomaGamingApiController::class, 'postLogExceptionArchive']);

});
