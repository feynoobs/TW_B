<?php

use App\Http\Controllers\Api\BoardListController;
use App\Http\Controllers\Api\GroupListController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api']], function (): void {
    Route::get('/groups', GroupListController::class);
    Route::get('/groups/{slug}/boards', BoardListController::class);
});
