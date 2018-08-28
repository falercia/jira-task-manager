<?php

Route::get('/', function () {
   return view('welcome');
});

Route::get('/admin/indicators', function () {
   return view('admin.daily_meeting');
});

//Route::get('/admin/indicators', function () {
//   return view('admin.indicators');
//});

/*Route::get('/admin/indicators', 'API\v1\QueryController@getIndicators');
Route::get('/admin/time_tracking', 'API\v1\QueryController@getUsersTimeTracking');
Route::get('/admin/productivity', 'API\v1\QueryController@showProductivityScreen');
Route::post('/admin/time-tracking-detail', 'API\v1\QueryController@getUserTimeTrackingDetail');
 * 
 */