<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//users auth

Route::group([

    'middleware' => 'api',
    'prefix' => 'user'

], function ($router) {

    Route::post('login', 'AuthController@login')->name('login');
    Route::post('register', 'AuthController@register')->name('register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me')->name('me');
    Route::post('check_code', 'AuthController@check_code');
    Route::post('update_user/{id}', 'AuthController@update_user');
    Route::post('resendCode', 'AuthController@resendCode');
    Route::put('update-profile', 'AuthController@update_profile');
    Route::post('send-code-reset-password', 'AuthController@send_code_reset_password');
    Route::post('reset-password', 'AuthController@reset_password');


});

Route::group([

    'prefix' => 'user',

], function () {

    Route::resource('main-category', 'mainCategoryController');

    Route::resource('event', 'EventController');

    Route::get('event/{event_id}/media','EventController@EventMedia');
    Route::post('event/{id}', 'EventController@update');

    Route::resource('ads', 'AdsController');
    Route::put('add_views/{id}', 'AdsController@StepViews');
    Route::get('slider', 'AdsController@Slider');

    Route::post('update-ads/{id}', 'AdsController@update');

    Route::resource('ads-category', 'AdsCategoryController');

    Route::resource('country', 'CountryController');

    Route::resource('region', 'RegionController');

    Route::resource('flash-ads', 'FlashAdsController');

    Route::resource('favourite', 'FavouriteController')->middleware(['assign.guard:users', 'jwt.auth']);

    Route::resource('notification', 'NotificationController');
//        ->middleware(['assign.guard:users', 'jwt.auth'])

    Route::get('notification_count', 'NotificationCountController@NotificationCount');
    Route::resource('support', 'SupportController');

    Route::resource('ask-ad', 'AskAdController');

    Route::resource('ask-special-event', 'AskSpecialEventController');

    Route::resource('pages', 'PagesController');


});


//admin auth

Route::group([

    'middleware' => 'api',
    'prefix' => 'admin'

], function ($router) {
    /* Admin */

    Route::post('adminLogin', 'Admin\AuthController@adminLogin');
    Route::get('admins', 'Admin\AuthController@admins');
    Route::delete('destroy/{id}', 'Admin\AuthController@destroy');
    Route::delete('trached/{id}', 'Admin\AuthController@trached');
    Route::delete('cancel_trached/{id}', 'Admin\AuthController@cancel_trached');

    Route::post('adminRegister', 'Admin\AuthController@adminRegister');
    Route::post('editAdmin/{id}', 'Admin\AuthController@editAdmin');


    Route::post('logout', 'Admin\AuthController@logout');
    Route::post('refresh', 'Admin\AuthController@refresh');
    Route::post('me', 'Admin\AuthController@me');


});

Route::group([

    'prefix' => 'admin',
//    'middleware' => ['assign.guard:admins', 'jwt.auth'],
    'namespace' => 'Admin'

], function () {
    //reset password
    Route::post('reset_password', 'AuthController@reset_password');

    Route::resource('event', 'EventController');

    //force delete event
    Route::delete('event-destroy/{id}', 'EventController@destroy');

    Route::put('special-event/{id}', 'EventController@SpecialEvent');
    Route::put('event_add_image/{id}', 'EventController@event_ad_image');
    Route::put('remove_ad_event/{id}', 'EventController@remove_add_event');

    //soft delte event
    Route::delete('event-trached/{id}', 'EventController@trached');

    //restore event
    Route::delete('event-restore/{id}', 'EventController@cancel_trached');

    Route::post('send-notification/{id}', 'EventController@sendNoti');

    Route::resource('country', 'CountryController');

    Route::post('country/{id}', 'CountryController@update');


    Route::resource('region', 'RegionController');

    Route::resource('ads', 'AdsController');
    Route::delete('ads/delete_images/{id}', 'AdsController@DeleteAdsImage');
    Route::put('ads/{id}/fake_views', 'AdsController@FakeViews');
    Route::put('ads/{id}/pin', 'AdsController@Pain');
    Route::get('ads/changeState/{id}/{state}', 'AdsController@Ads_State');

    Route::post('ads/{id}', 'AdsController@update');

    Route::resource('ads-category', 'AdsCategoryController');

    Route::post('ads-category/{id}', 'AdsCategoryController@update');

    Route::resource('main_category', 'main_categoriesController');

    Route::post('main_category/{id}', 'main_categoriesController@update');

    // ads Notifications
    Route::get('get_notifications', 'AdsNotificationsController@get');
    Route::post('ads_notifications', 'AdsNotificationsController@set');
    Route::delete('delete_notifications/{id}', 'AdsNotificationsController@DeleteNotification');

    Route::get('fetch_region/{country_id}', 'AdsNotificationsController@RegionByCountryID');

//    Route::get('test_notification_country/{country_id}/{text}', 'AdsNotificationsController@TopicNotification');
    Route::get('test_notification_region/{region_id}/{text}', 'AdsNotificationsController@TopicNotification');


    Route::resource('flash-ads', 'flash_adsController');

    Route::post('flash-ads/{id}', 'flash_adsController@update');
    Route::put('flash-ads/{id}/add_views', 'flash_adsController@AddViews');
    Route::resource('support', 'supportController');

    Route::resource('pages', 'pagesController');

    Route::resource('ask-ads', 'askAdsController');

    Route::resource('ask-special-event', 'askSpecialEventController');

    Route::resource('special-events', 'specialEventsController');


});


