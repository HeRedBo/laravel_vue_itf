<?php
//Route::get('/', 'IndexController@index');
Route::post('/login', 'LoginController@login');
Route::get('/ApiTest/api-test', 'ApiTestController@ApiTest');
Route::get('/ApiTest/up-base64-img', 'ApiTestController@uploadBase64Img');


Route::group(['middleware' => ['auth:admin']], function() {

    Route::get('/', 'IndexController@index');

    if (!Request::ajax()) {
        Route::get('{path?}', ['uses' => 'IndexController@index'])->where('path', '[\/\w\.-]*');
    }

    // file upload
    Route::get('venue/edit ', ['as' => 'admin.venue.edit', 'uses' => 'VenueController@edit']);
    Route::resource('venue', 'VenueController');
    Route::get('/upload/index', 'UploadController@index');
    Route::post('/upload/uploadImg', 'UploadController@uploadImg');
    Route::get('/upload/file-detail', 'UploadController@fileDetail');
    Route::get('/upload/file-list', 'UploadController@getFileList');

    // role
    Route::get('role/edit ', ['as' => 'admin.role.edit', 'uses' => 'RolesController@edit']);
    Route::get('role/getAcl ', ['as' => 'admin.role.getAcl', 'uses' => 'RolesController@getAcl']);
    Route::resource('role', 'RolesController');

    // permission
    Route::get('permission/edit ', ['as' => 'admin.role.permission', 'uses' => 'PermissionsController@edit']);
    Route::resource('permission', 'PermissionsController');

    // admin
    Route::get('admin/edit', ['as' => 'admin.admin.edit', 'uses' => 'AdminController@edit']);
    Route::get('admin/role', ['as' => 'admin.admin.role', 'uses' => 'AdminController@role']);
    Route::get('admin/venues', ['as' => 'admin.admin.role', 'uses' => 'AdminController@venues']);
    Route::resource('admin', 'AdminController');
    
});










if (!Request::ajax()) {
    Route::get('{path?}', ['uses' => 'IndexController@index'])->where('path', '[\/\w\.-]*');
}

