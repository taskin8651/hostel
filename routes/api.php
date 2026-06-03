<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AuthApiController;
use App\Http\Controllers\Api\V1\Admin\FoodMenusApiController;
use App\Http\Controllers\Api\V1\Admin\EventsApiController;
use App\Http\Controllers\Api\V1\Admin\NoticesApiController;
use App\Http\Controllers\Api\V1\Admin\StudentAttendanceApiController;
use App\Http\Controllers\Api\V1\Admin\VisitorsApiController;
use App\Http\Controllers\Api\V1\Admin\LeavesApiController;
use App\Http\Controllers\Api\V1\Admin\ComplaintsApiController;
use App\Http\Controllers\Api\V1\Admin\FeePaymentsApiController;

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

    // VISITORS
    Route::get('/visitors', [VisitorsApiController::class, 'index']);
    Route::post('/visitors/create', [VisitorsApiController::class, 'store']);
    Route::get('/visitors/{id}', [VisitorsApiController::class, 'show']);

    // LEAVES 
    Route::get('/leaves', [LeavesApiController::class, 'index']); 
    Route::post('/leaves/apply', [LeavesApiController::class, 'store']); 
    Route::get('/leaves/{id}', [LeavesApiController::class, 'show']);

    // COMPLAINTS
    Route::get('/complaints', [ComplaintsApiController::class, 'index']); 
    Route::post('/complaints/create', [ComplaintsApiController::class, 'store']); 
    Route::get('/complaints/{id}', [ComplaintsApiController::class, 'show']);

    // FEE PAYMENTS
    Route::get('/fee-payments', [FeePaymentsApiController::class, 'index']); 
    Route::post('/fee-payments/create', [FeePaymentsApiController::class, 'store']); 
    Route::get('/fee-payments/{id}', [FeePaymentsApiController::class, 'show']);




});