<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {
    Route::post('login', 'AuthApiController@login')->name('login');

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('logout', 'AuthApiController@logout')->name('logout');
        Route::get('profile', 'AuthApiController@profile')->name('profile');

        $hostelApiControllers = [
            'students'           => 'StudentsApiController',
            'rooms'              => 'RoomsApiController',
            'beds'               => 'BedsApiController',
            'room-allocations'   => 'RoomAllocationsApiController',
            'accessories'        => 'AccessoriesApiController',
            'student-attendance' => 'StudentAttendanceApiController',
            'staff-attendance'   => 'StaffAttendanceApiController',
            'visitors'           => 'VisitorsApiController',
            'fees'               => 'FeesApiController',
            'fee-payments'       => 'FeePaymentsApiController',
            'complaints'         => 'ComplaintsApiController',
            'leaves'             => 'LeavesApiController',
            'staff'              => 'StaffApiController',
            'food-menus'         => 'FoodMenusApiController',
            'staff-payments'     => 'StaffPaymentsApiController',
            'staff-works'        => 'StaffWorksApiController',
            'expenses'           => 'ExpensesApiController',
            'hostel-expenses'    => 'HostelExpensesApiController',
            'incomes'            => 'IncomesApiController',
            'bills'              => 'BillsApiController',
            'notices'            => 'NoticesApiController',
            'events'             => 'EventsApiController',
        ];

        foreach ($hostelApiControllers as $uri => $controller) {
            Route::get($uri, $controller . '@index')->name($uri . '.index');
            Route::post($uri, $controller . '@store')->name($uri . '.store');
            Route::get($uri . '/{id}', $controller . '@show')->name($uri . '.show');
            Route::put($uri . '/{id}', $controller . '@update')->name($uri . '.update');
            Route::patch($uri . '/{id}', $controller . '@update')->name($uri . '.patch');
            Route::put($uri . '/{id}/status', $controller . '@updateStatus')->name($uri . '.status');
            Route::delete($uri . '/{id}', $controller . '@destroy')->name($uri . '.destroy');
        }

        // SOW-friendly aliases for singular endpoint names.
        Route::get('room-allocation', 'RoomAllocationsApiController@index');
        Route::post('room-allocation', 'RoomAllocationsApiController@store');
        Route::get('room-allocation/{id}', 'RoomAllocationsApiController@show');
        Route::put('room-allocation/{id}', 'RoomAllocationsApiController@update');
        Route::delete('room-allocation/{id}', 'RoomAllocationsApiController@destroy');
        Route::get('staff-work', 'StaffWorksApiController@index');
        Route::post('staff-work', 'StaffWorksApiController@store');
        Route::get('staff-work/{id}', 'StaffWorksApiController@show');
        Route::put('staff-work/{id}', 'StaffWorksApiController@update');
        Route::delete('staff-work/{id}', 'StaffWorksApiController@destroy');

        Route::get('{module}', 'HostelModuleApiController@index')->name('hostel.index');
        Route::post('{module}', 'HostelModuleApiController@store')->name('hostel.store');
        Route::get('{module}/{id}', 'HostelModuleApiController@show')->name('hostel.show');
        Route::put('{module}/{id}', 'HostelModuleApiController@update')->name('hostel.update');
        Route::patch('{module}/{id}', 'HostelModuleApiController@update')->name('hostel.patch');
        Route::put('{module}/{id}/status', 'HostelModuleApiController@updateStatus')->name('hostel.status');
        Route::delete('{module}/{id}', 'HostelModuleApiController@destroy')->name('hostel.destroy');
    });
});
