<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});
 
Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    // Hostel Management
    $hostelControllers = [
        'students'           => 'StudentsController',
        'rooms'              => 'RoomsController',
        'beds'               => 'BedsController',
        'room-allocations'   => 'RoomAllocationsController',
        'accessories'        => 'AccessoriesController',
        'student-attendance' => 'StudentAttendanceController',
        'staff-attendance'   => 'StaffAttendanceController',
        'visitors'           => 'VisitorsController',
        'fees'               => 'FeesController',
        'fee-payments'       => 'FeePaymentsController',
        'complaints'         => 'ComplaintsController',
        'leaves'             => 'LeavesController',
        'staff'              => 'StaffController',
        'food-menus'         => 'FoodMenusController',
        'staff-payments'     => 'StaffPaymentsController',
        'staff-works'        => 'StaffWorksController',
        'expenses'           => 'ExpensesController',
        'hostel-expenses'    => 'HostelExpensesController',
        'incomes'            => 'IncomesController',
        'bills'              => 'BillsController',
        'notices'            => 'NoticesController',
        'events'             => 'EventsController',
    ];

    foreach ($hostelControllers as $uri => $controller) {
        Route::delete($uri . '/destroy', $controller . '@massDestroy')->name($uri . '.massDestroy');
        Route::resource($uri, $controller);
    }

    Route::get('hostel/reports/{report}', 'HostelModuleController@report')->name('hostel.reports.show');
    Route::get('hostel/fee-payments/{id}/receipt', 'HostelModuleController@receipt')->name('hostel.fee-payments.receipt');
    Route::delete('hostel/{module}/destroy', 'HostelModuleController@massDestroy')->name('hostel.modules.massDestroy');
    Route::get('hostel/{module}', 'HostelModuleController@index')->name('hostel.modules.index');
    Route::get('hostel/{module}/create', 'HostelModuleController@create')->name('hostel.modules.create');
    Route::post('hostel/{module}', 'HostelModuleController@store')->name('hostel.modules.store');
    Route::get('hostel/{module}/{id}', 'HostelModuleController@show')->name('hostel.modules.show');
    Route::get('hostel/{module}/{id}/edit', 'HostelModuleController@edit')->name('hostel.modules.edit');
    Route::put('hostel/{module}/{id}', 'HostelModuleController@update')->name('hostel.modules.update');
    Route::delete('hostel/{module}/{id}', 'HostelModuleController@destroy')->name('hostel.modules.destroy');

    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
