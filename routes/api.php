<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Api\AuthControllerApi;
use App\Http\Controllers\Backend\Api\Project\ProjectControllerApi;

Route::get('/users', [AuthControllerApi::class, 'index']);
Route::post('/login', [AuthControllerApi::class, 'login']);
Route::post('/store', [AuthControllerApi::class, 'store']);
Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout', [AuthControllerApi::class, 'logout']);
    Route::post('/create_staff/{id}', [AuthControllerApi::class, 'createStaff']);
    Route::get('/get_list_staff/{id}', [AuthControllerApi::class, 'getListStaff']);
    Route::group(['prefix' => 'project'], function () {
        Route::post('/create_project/{id}', [ProjectControllerApi::class, 'store']);
        Route::post('/{id}/add_staff_boss', [ProjectControllerApi::class, 'addStaffBoss']);
        Route::get('/get_project/{role}/{id}', [ProjectControllerApi::class, 'getListProject']);
        Route::get('/{id}/detail', [ProjectControllerApi::class, 'getProjectDetail']);
        Route::post('/{id}/delete_staff_boss', [ProjectControllerApi::class, 'deleteStaffBossFromProject']);
        Route::post('/{project_id}/edit_{role}/{id}', [ProjectControllerApi::class, 'editStaffBossInProject']);
        Route::post('/{project_id}/edit', [ProjectControllerApi::class, 'editProject']);
        Route::delete('/{project_id}/delete', [ProjectControllerApi::class, 'deleteProject']);
    });
});
