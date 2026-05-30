<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Epaper
    Route::post('epapers/media', 'EpaperApiController@storeMedia')->name('epapers.storeMedia');
    Route::apiResource('epapers', 'EpaperApiController');
});
