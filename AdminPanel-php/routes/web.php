<?php

use App\Http\Controllers\LanguageController;

/*
 * Global Routes
 * Routes that are used between both frontend and backend.
 */

// Switch between the included languages
Route::get('lang/{lang}', [LanguageController::class, 'swap']);

/*
 * Frontend Routes
 * Namespaces indicate folder structure
 */
Route::group(['namespace' => 'Frontend', 'as' => 'frontend.'], function () {
    include_route_files(__DIR__.'/frontend/');
});

/*
 * Backend Routes
 * Namespaces indicate folder structure
 */
Route::group(['namespace' => 'Backend', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    /*
     * These routes need view-backend permission
     * (good if you want to allow more than one group in the backend,
     * then limit the backend features by different roles or permissions)
     *
     * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
     * These routes can not be hit if the password is expired
     */
    include_route_files(__DIR__.'/backend/');
});








Route::resource('admin/user-subscriptions', 'Backend\\UserSubscriptionsController');
Route::resource('admin/payments', 'Backend\\PaymentsController');
Route::resource('admin/payments_-check', 'Backend\\Payments_CheckController');
Route::resource('admin/rad-acct', 'Backend\\RadAcctController');
Route::resource('admin/rad-check', 'Backend\\RadCheckController');
Route::resource('admin/rad-group-reply', 'Backend\\RadGroupReplyController');
Route::resource('admin/rad-post-auth', 'Backend\\RadPostAuthController');
Route::resource('admin/rad-user-group', 'Backend\\RadUserGroupController');
Route::resource('admin/n-a-s', 'Backend\\NASController');
//Route::resource('admin/user-servers', 'Backend\\UserServersController');
#Route::resource('admin/user-radius-attributes', 'Backend\\UserRadiusAttributesController');
#Route::resource('admin/user-subscription-extensions', 'Backend\\UserSubscriptionExtensionsController');
Route::resource('admin/radius-default-attributes', 'Backend\\RadiusDefaultAttributesController');
#Route::resource('admin/user-history', 'Backend\\UserHistoryController');
#Route::resource('admin/payment-info', 'Backend\\PaymentInfoController');
Route::resource('admin/s-m-t-p2-g-o-email-data', 'Backend\\SMTP2GOEmailDataController');
#Route::resource('admin/s-m-t-p2-g-o-email-data', 'Backend\\SMTP2GOEmailDataController');
Route::resource('appSignupReports', 'AppSignupReportController');
Route::resource('ltvreport', 'LTVReportController');


