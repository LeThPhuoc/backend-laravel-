<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Api\AuthControllerApi;

Route::get('/users', [AuthControllerApi::class, 'index']);
Route::post('/login', [AuthControllerApi::class, 'login']);
Route::post('/store', [AuthControllerApi::class, 'store']);
Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout', [AuthControllerApi::class, 'logout']);
    Route::post('/create_staff', [AuthControllerApi::class, 'createStaff']);
    Route::get('/get_list_staff', [AuthControllerApi::class, 'getListStaff']);
});
