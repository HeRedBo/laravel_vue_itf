<?php
//Route::get('/', 'IndexController@index');
Route::post('/login', 'LoginController@login');
Route::get('/ApiTest/api-test', 'ApiTestController@ApiTest');
Route::get('/ApiTest/up-base64-img', 'ApiTestController@uploadBase64Img');


Route::group(['middleware' => ['auth:admin','menu']], function() {

    Route::get('/', 'IndexController@index');
    Route::get('/menu', ['as' => 'admin.menu', 'uses' => 'IndexController@menu']);
});

Route::group(['middleware' => ['auth:admin']], function() {
    
    if (!Request::ajax()) {
        Route::get('{path?}', ['uses' => 'IndexController@index'])->where('path', '[\/\w\.-]*');
    }
    
   
    Route::get('venue/edit ', ['as' => 'admin.venue.edit', 'uses' => 'VenueController@edit']);
    Route::get('venue/getVenues ', ['as' => 'admin.venue.getVenues', 'uses' => 'VenueController@getVenueOptions']);
    Route::get('venue/checkName ', ['as' => 'admin.venue.checkName', 'uses' => 'VenueController@checkVenueName']);
    Route::resource('venue', 'VenueController');
    
    // file upload
    Route::get('/upload/index', 'UploadController@index');
    Route::post('/upload/uploadImg', 'UploadController@uploadImg');
    Route::post('/upload/uploadImg', 'UploadController@uploadImg');
    Route::post('/upload/upAvatar', 'UploadController@uploadAvatar');
    
    Route::get('/upload/file-detail', 'UploadController@fileDetail');
    Route::get('/upload/file-list', 'UploadController@getFileList');

    // role
    Route::get('role/edit ', ['as' => 'admin.role.edit', 'uses' => 'RolesController@edit']);
    Route::get('role/getAcl', ['as' => 'admin.role.getAcl', 'uses' => 'RolesController@getAcl']);
    Route::post('role/setAcl', ['as' => 'admin.role.getAcl', 'uses' => 'RolesController@setAcl']);
    Route::get('role/checkName ', ['as' => 'admin.role.checkName', 'uses' => 'RolesController@checkRoleName']);
    Route::resource('role', 'RolesController');

    // permission
    Route::get('permission/edit ', ['as' => 'admin.permission.edit', 'uses' => 'PermissionsController@edit']);
    Route::resource('permission', 'PermissionsController');

    // admin
    Route::get('user/edit', ['as' => 'admin.user.edit', 'uses' => 'AdminController@edit']);
    Route::get('user/role', ['as' => 'admin.user.role', 'uses' => 'AdminController@role']);
    Route::get('user/venues', ['as' => 'admin.user.role', 'uses' => 'AdminController@venues']);
    Route::get('user/checkUserName', ['as' => 'admin.user.role', 'uses' => 'AdminController@checkUserName']);

    Route::resource('user', 'AdminController');
    
});
