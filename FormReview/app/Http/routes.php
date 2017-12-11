<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::get('/php', function () {
    echo phpinfo();
    return;
});

Route::group(['middleware' => ['web']], function () {

});

Route::group(['middleware' => 'web'], function () {
    Route::auth();
    Route::any('test', 'Pub\TestController@index');

    Route::get('/sendCode/{type}','Auth\AuthController@sendCode');
    Route::get('/', 'WelcomeController@index');
    Route::get('/home', 'HomeController@index')->name('home.index');
    Route::post('/home/save-info', 'HomeController@saveInfo')->name('home.saveInfo');
    Route::post('/home/update', 'HomeController@update')->name('home.update');
    Route::get('/home/bindWechat/{id}/{key}', 'HomeController@bindWechat');
});

Route::group(['middleware' => 'web', 'namespace' => 'Pub', 'prefix' => 'pub'], function () {
    Route::get('{fid}/info', 'InfoController@create')->name('pub.info.create');
    Route::post('{fid}/info', 'InfoController@store')->name('pub.info.store');
    Route::post('info/{id}', 'InfoController@update')->name('pub.info.update');
    Route::resource('info', 'InfoController',['except' => ['index','create','store']]);
});


Route::group(['middleware' => 'web', 'namespace' => 'Web', 'prefix' => 'web'], function() {
    Route::any('review/store', 'ReviewController@store');
    Route::any('review/remark', 'ReviewController@remark');
    Route::get('review/getReviews', 'ReviewController@getReviews');
    Route::any('form/{id}/datalist', 'FormController@datalist')->name('web.form.datalist');
    Route::any('form/{id}/datadata', 'FormController@dataData')->name('web.form.datadata');
    Route::any('form/{id}/noticeSetting', 'FormController@noticeSetting')->name('web.form.noticeSetting');
    Route::post('form/{id}/saveNotice', 'FormController@saveNotice')->name('web.form.saveNotice');
    Route::get('form/{id}/getNotice', 'FormController@getNotice')->name('web.form.getNotice');
    Route::any('form/{id}/publish', 'FormController@publish')->name('web.form.publish');
    Route::any('form/{id}/report', 'FormController@report')->name('web.form.report');
    Route::any('form/{id}/getStat', 'FormController@getStat')->name('web.form.getStat');
    Route::any('form/{id}/cancel', 'FormController@cancel')->name('web.form.cancel');
    Route::post('form/preview', 'FormController@preview');
    Route::get('form/preview/{id}', 'FormController@preview');
    Route::post('form/ajax-update', 'FormController@ajaxUpdate');
    Route::post('form/limit', 'FormController@limit');
    Route::get('form/history', 'FormController@history');
    Route::get('form/{id}/custom', 'FormController@custom')->name('web.form.custom');//自定义样式
    Route::any('form/upload/{type}', 'FormController@upload')->name('web.form.upload');//上传文件
    Route::post('form/{id}/saveFiled', 'FormController@saveFiled')->name('web.form.saveFiled');
    Route::get('form/customReviewList', 'FormController@customReviewList')->name('web.form.customReviewList');
    Route::post('form/customReview', 'FormController@customReview')->name('web.form.customReview');
    Route::resource('form', 'FormController');


    Route::get('admin/{fid}/manageAdmins', 'FormAdminController@manageAdmins')->name('web.admin.manageAdmins');
    Route::get('admin/{fid}/getAdmins', 'FormAdminController@getAdmins');
    Route::get('admin/message', 'FormAdminController@message')->name('web.message');
    Route::post('admin/deleteMsg', 'FormAdminController@deleteMsg')->name('web.message.deleteMsg');
    Route::post('admin/handle', 'FormAdminController@handle')->name('web.handle');
    Route::get('admin/inviteMsgNum', 'FormAdminController@inviteMsgNum')->name('web.inviteMsgNum');
    Route::any('admin/getExceptUsers', 'FormAdminController@getExceptUsers');
    Route::get('admin/getOwnForms', 'FormAdminController@getOwnForms');
    Route::get('admin/getAuthorities', 'FormAdminController@getAuthorities');
    Route::resource('admin', 'FormAdminController');

    Route::get('wechat/test', 'WechatController@test')->name('web.wechat.test');
    Route::get('wechat/index', 'WechatController@showPlatforms')->name('web.wechat.index');
    Route::any('wechat/auth', 'WechatController@auth')->name('web.wechat.auth');
    Route::any('wechat/callback/{appid}', 'WechatController@callback')->name('web.wechat.callback');
    Route::get('wechat/templates', 'WechatController@showTemplates')->name('web.wechat.template');
    Route::get('wechat/getPlatforms', 'WechatController@getPlatforms')->name('web.wechat.getPlatforms');
    Route::get('wechat/add-platform', 'WechatController@addPlatform')->name('web.wechat.addPlatform');
    Route::any('wechat/save-platform', 'WechatController@savePlatform')->name('web.wechat.savePlatform');
    Route::post('wechat/edit-platform', 'WechatController@editPlatform')->name('web.wechat.editPlatform');
    Route::delete('wechat/{id}', 'WechatController@destroy')->name('web.wechat.destroy');
    Route::get('wechat/api_component_token', 'WechatController@api_component_token')->name('web.wechat.api_component_token');
});


