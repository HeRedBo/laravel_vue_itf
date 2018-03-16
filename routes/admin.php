<?php

Route::get('/login', ['uses' => 'IndexController@index'])->name('login');
Route::post('/login', ['as' => 'admin.login','uses' => 'LoginController@login']);
Route::get('/logout', 'LoginController@logout'); //退出系统
Route::get('/ApiTest/api-test', 'ApiTestController@ApiTest');
Route::get('/ApiTest/up-base64-img', 'ApiTestController@uploadBase64Img');
Route::get('/ApiTest/save-operator-log', 'ApiTestController@saveOperatorLog');

Route::group(['middleware' => ['auth:admin','menu']], function() {
    Route::get('/', 'IndexController@index');
    Route::any('/menu', ['as' => 'admin.menu', 'uses' => 'IndexController@menu']);
    Route::get('/checkAcl', ['as' => 'admin.acl', 'uses' => 'IndexController@checkAcl']);
});

if (!Request::ajax()) {
    Route::get('{path?}', ['uses' => 'IndexController@index'])->where('path', '[\/\w\.-]*');
}

Route::group(['middleware' => ['auth:admin','authAdmin']], function() {
    
    // index statistics
    Route::get('/statistics', ['as' => 'admin.statistics', 'uses' => 'IndexController@statistics']);
    //Route::get('venue/edit ', ['as' => 'admin.venue.edit', 'uses' => 'VenueController@edit']);
    Route::get('venue/getVenues ', ['as' => 'admin.venue.getVenues', 'uses' => 'VenueController@getVenueOptions']);
    Route::get('venue/checkName ', ['as' => 'admin.venue.checkName', 'uses' => 'VenueController@checkVenueName']);
    Route::resource('venue', 'VenueController');
    
    // file upload
    Route::get('/upload/index', 'UploadController@index');
    Route::post('/upload/uploadImg', 'UploadController@uploadImg');
    Route::post('/upload/uploadImg', 'UploadController@uploadImg');

    Route::get('/upload/file-detail', 'UploadController@fileDetail');
    Route::get('/upload/file-list', 'UploadController@getFileList');

    Route::post('/upload/upAvatar', ['as' => 'admin.upload.upAvatar', 'uses' => 'UploadController@uploadAvatar']);

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
    Route::get('user/venues', ['as' => 'admin.user.venues', 'uses' => 'AdminController@venues']);
    Route::get('user/userVenues', ['as' => 'admin.user.userVenues', 'uses' => 'AdminController@getUserVenues']);
    Route::get('user/checkUserName', ['as' => 'admin.user.checkUserName', 'uses' => 'AdminController@checkUserName']);
    Route::get('user/logger', ['as' => 'admin.user.logger', 'uses' => 'AdminController@logger']);

    Route::resource('user', 'AdminController');

    // class
    Route::get('class/checkClassName', ['as' => 'admin.class.checkClassName', 'uses' => 'ClassesController@checkClassName']);
    Route::get('class/classOptions', ['as' => 'admin.class.classOptions', 'uses' => 'ClassesController@getClassOptions']);
    Route::resource('class', 'ClassesController');
    
    // card 卡券
    Route::get('card/checkCardName', ['as' => 'admin.card.checkCardName', 'uses' => 'CardsController@checkCardName']);
    Route::get('card/cardOptions', ['as' => 'admin.card.cardOptions', 'uses' => 'CardsController@getCardOptions']);
    Route::post('card/changeStatus', ['as' => 'admin.card.changeStatus', 'uses' => 'CardsController@changeStatus']);
    Route::get('card/cardLogger', ['as' => 'admin.card.cardLogger', 'uses' => 'CardsController@cardLogger']);
    Route::get('card/cardTypeOptions', ['as' => 'admin.card.cardTypeOptions', 'uses' => 'CardsController@cardTypeOptions']);
    Route::get('card/studentCardOptions', ['as' => 'admin.card.studentCardOptions', 'uses' => 'CardsController@studentCardOptions']);
    Route::resource('card', 'CardsController');
    
    // students
    Route::get('student/relationOptions', ['as' => 'admin.student.relationOptions', 'uses' => 'StudentsController@relationOptions']);
    Route::get('student/sexOptions', ['as' => 'admin.student.sexOptions', 'uses' => 'StudentsController@sexOptions']);
    Route::get('student/statusOptions', ['as' => 'admin.student.statusOptions', 'uses' => 'StudentsController@statusOptions']);
    Route::get('student/getStudentInfo', ['as' => 'admin.student.getStudentInfo', 'uses' => 'StudentsController@getStudentInfo']);
    Route::get('student/studentCardList', ['as' => 'admin.student.studentCardList', 'uses' => 'StudentsController@studentCardList']);
    Route::post('student/saveStudentCard', ['as' => 'admin.student.saveStudentCard', 'uses' => 'StudentsController@saveStudentCard']);
    Route::post('student/sign', ['as' => 'admin.student.sign', 'uses' => 'StudentsController@sign']);
    Route::get('student/getSignCalendar', ['as' => 'admin.student.getSignCalendar', 'uses' => 'StudentsController@getSignCalendar']);
    Route::get('student/signClassOptions', ['as' => 'admin.student.signClassOptions', 'uses' => 'StudentsController@signClassOptions']);
    Route::post('student/changeStudentCardStatus', ['as' => 'admin.student.changeStudentCardStatus', 'uses' => 'StudentsController@changeStudentCardStatus']);
    Route::get('student/cardLogger', ['as' => 'admin.student.cardLogger', 'uses' => 'StudentsController@cardLogger']);

    Route::resource('student', 'StudentsController');
    
    Route::post('venueBill/createDataType', ['as' => 'admin.venueBill.createDataType', 'uses' => 'VenueBillController@createVenueBillDataType']);
    Route::resource('venueBill', 'VenueBillController');
    
    // venueschedule
    Route::get('venueSchedule/changeStatus', ['as' => 'admin.venueSchedule.changeStatus', 'uses' => 'VenueSchedulesController@changeStatus']);
    Route::get('venueSchedule/schedule', ['as' => 'admin.venueSchedule.schedule', 'uses' => 'VenueSchedulesController@schedules']);
    Route::post('venueSchedule/saveScheduleExtend', ['as' => 'admin.venueSchedule.saveScheduleExtend', 'uses' => 'VenueSchedulesController@saveScheduleExtend']);
    Route::resource('venueSchedule', 'VenueSchedulesController');

});
