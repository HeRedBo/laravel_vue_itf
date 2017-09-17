<?php
//Route::get('/', 'IndexController@index');


if (!Request::ajax()) {
    Route::get('{path?}', ['uses' => 'IndexController@index'])->where('path', '[\/\w\.-]*');
}
