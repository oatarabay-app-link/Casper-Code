<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\DashboardController;
use \App\Http\Controllers\Backend\API\APIController;
use \App\Http\Controllers\Backend\API\AuthHandlerController;
use \App\Http\Controllers\Backend\API\UsersController;
use \App\Http\Controllers\Backend\API\VPNServersController;

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


// Auth Based Routes
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




// Login Routes
Route::post('/auth/token', [AuthHandlerController::class, 'auth_token'])->name('auth_token');
// Refreshing Token
Route::put('/auth/token', [AuthHandlerController::class, 'refresh_token'])->name('refresh_token');


//Users Functions

//Save Aff Ref to Users Table
Route::middleware('auth:api')->get('/users/saveref/{aff_ref}', '\App\Http\Controllers\Backend\API\UsersController@affiliate_ref')->name('affiliate_ref');

Route::middleware('auth:api')->get('/users/profile/', '\App\Http\Controllers\Backend\API\UsersController@user_profile')->name('user_profile');

//Payment Routes

Route::middleware('auth:api')->post('/payment/twocheckout/code/generate/', '\App\Http\Controllers\Backend\API\PaymentController@generate_payment')->name('generate_payment');
Route::middleware('auth:api')->get('/payment/twocheckout/code/getPayment', '\App\Http\Controllers\Backend\API\PaymentController@get_payment')->name('get_payment');

//code/checkoutApps/
Route::middleware('auth:api')->get('/payment/twocheckout/checkoutApps', '\App\Http\Controllers\Backend\API\PaymentController@checkoutApps')->name('checkout_apps');

///code/getPayment

# VPN
Route::get('/vpn/servers', [VPNServersController::class, 'vpn_servers'])->name('vpn_servers');
Route::get('/vpn/servers/foruser', [VPNServersController::class, 'vpn_servers_for_user'])->name('vpn_servers');


// Public Routes

Route::post('/registration/users', [\App\Http\Controllers\Backend\API\UsersController::class, 'register_user'])->name('register_user');
Route::post('/registration/users/private', [\App\Http\Controllers\Backend\API\UsersController::class, 'register_user_confirmation'])->name('register_user_confirmation');
Route::put('/registration/users/{email}/resendConfirmEmail', [\App\Http\Controllers\Backend\API\UsersController::class, 'resend_confirm_email'])->name('resend_confirm_email');
//Reset Password Mail for user from API
Route::post('/registration/users/{email}/resetpassword', [\App\Http\Controllers\Backend\API\UsersController::class, 'reset_password'])->name('reset_password');



Route::get('/users/intercominfo', [\App\Http\Controllers\Backend\API\IntercomController::class, 'intercom_info'])->name('intercom_info');

Route::get('/subscriptions/appversion', [APIController::class, 'versions'])->name('appversion');
Route::get('/subscriptions/getserverdate', [APIController::class, 'get_server_time'])->name('get_server_time');
///subscriptions
Route::get('/subscriptions', [\App\Http\Controllers\Backend\API\SubscriptionController::class, 'list_subscriptions'])->name('list_subscriptions');
