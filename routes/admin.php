<?php
//Route::get('/', 'IndexController@index');

Route::get('/ApiTest/api-test', 'ApiTestController@ApiTest');

if (!Request::ajax()) {
    Route::get('{path?}', ['uses' => 'IndexController@index'])->where('path', '[\/\w\.-]*');
}

