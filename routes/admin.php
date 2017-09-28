<?php
//Route::get('/', 'IndexController@index');
Route::post('/login', 'LoginController@login');
Route::get('/ApiTest/api-test', 'ApiTestController@ApiTest');
Route::get('/ApiTest/up-base64-img', 'ApiTestController@uploadBase64Img');

// file upload
Route::get('venue/edit ', ['as' => 'admin.venue.edit', 'uses' => 'VenueController@edit']);
Route::resource('venue', 'VenueController');
Route::get('/upload/index', 'UploadController@index');


// role
Route::get('role/edit ', ['as' => 'admin.role.edit', 'uses' => 'RolesController@edit']);
Route::resource('role', 'RolesController');

// permission
Route::get('permission/edit ', ['as' => 'admin.role.permission', 'uses' => 'PermissionsController@edit']);
Route::resource('permission', 'PermissionsController');

// admin
Route::get('admins/edit ', ['as' => 'admins.admin.permission', 'uses' => 'AdminsController@edit']);
Route::resource('admins', 'AdminsController');


if (!Request::ajax()) {
    Route::get('{path?}', ['uses' => 'IndexController@index'])->where('path', '[\/\w\.-]*');
}

