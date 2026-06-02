<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AuthApiController;
use App\Http\Controllers\Api\V1\Admin\FoodMenusApiController;
use App\Http\Controllers\Api\V1\Admin\EventsApiController;
use App\Http\Controllers\Api\V1\Admin\NoticesApiController;
use App\Http\Controllers\Api\V1\Admin\StudentAttendanceApiController;

// LOGIN
Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // PROFILE
    Route::get('/profile/{user_id}', [AuthApiController::class, 'profile']);

    // LOGOUT
    Route::post('/logout/{user_id}', [AuthApiController::class, 'logout']);

    // FOOD MENUS
    Route::get('/food-menus', [FoodMenusApiController::class, 'index']); 
    Route::get('/food-menu/today', [FoodMenusApiController::class, 'todayMenu']); 
    Route::get('/food-menu/day/{day}', [FoodMenusApiController::class, 'dayMenu']);

    // EVENTS
    Route::get('/events', [EventsApiController::class, 'index']);
    Route::get('/events/today', [EventsApiController::class, 'todayEvents']);
    Route::get('/events/upcoming', [EventsApiController::class, 'upcomingEvents']);
    Route::get('/events/{id}', [EventsApiController::class, 'show']);

    // NOTICES
    Route::get('/notices', [NoticesApiController::class, 'index']);
    Route::get('/notices/latest', [NoticesApiController::class, 'latestNotices']);
    Route::get('/notices/{id}', [NoticesApiController::class, 'show']);

    // STUDENT ATTENDANCE
    Route::post('/student-attendance/create',[StudentAttendanceApiController::class, 'storeAttendance']);




});