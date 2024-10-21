<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\V1\JobController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'v1', 'namespace' => 'App\Http\Controllers\API\V1'], function () {
    Route::resource('jobs', JobController::class)->only(['index', 'store', 'show', 'destroy']);
});
