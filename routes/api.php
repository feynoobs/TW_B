<?php

use App\Http\Controllers\Api\BoardListController;
use App\Http\Controllers\Api\GroupListController;
use App\Http\Controllers\Api\ThreadListController;
use App\Http\Controllers\Api\ThreadCreateController;
use App\Http\Controllers\Api\ResponseListController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api']], function (): void {
    Route::get('/groups', GroupListController::class);
    Route::get('/groups/{slug}/boards', BoardListController::class);
    Route::get('/boards/{slug}/threads', ThreadListController::class);
    Route::post('/boards/{slug}/thread/create', ThreadCreateController::class);
    Route::post('/threads/{id}/responses', ResponseListController::class);
});
