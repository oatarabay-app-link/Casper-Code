<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ConnectedUsersController;
use App\Http\Controllers\Backend\IntercomMarketingDataController;
use App\Http\Controllers\Backend\CasperVPN\Reports\AppSignupReportController;

// All route names are prefixed with 'admin.'.
Route::redirect('/', '/admin/dashboard', 301);
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('connected-users', [ConnectedUsersController::class, 'index'])->name('connected_users');
Route::get('connected-users-by-country', [ConnectedUsersController::class, 'connected_users_by_country'])->name('connected_users_by_country');
Route::get('connected-users-per-server-by-country', [ConnectedUsersController::class, 'connected_users_per_server_by_country'])->name('connected_users_per_server_by_country');
Route::resource('protocols', 'ProtocolsController');
Route::resource('subscriptions', 'SubscriptionsController');
Route::resource('subscription-protocols', 'SubscriptionProtocolsController');
//Route::resource('user-subscriptions', 'UserSubscriptionsController');
Route::resource('v-p-n-servers', 'VPNServersController');
Route::resource('v-p-n-server-protocols', 'VPNServerProtocolsController');
Route::resource('service-providers', 'ServiceProvidersController');
Route::resource('services', 'ServicesController');
Route::resource('subscription-radius-attributes', 'SubscriptionRadiusAttributesController');
Route::resource('intercom-marketing-data', 'IntercomMarketingDataController');
Route::get('intercom-marketing-data-grid', [IntercomMarketingDataController::class, 'load_grid'])->name('admin.intercom-marketing-data.datag');
Route::get('reports/app-signups', [AppSignupReportController::class, 'index'])->name('admin.reports.app-signups');
Route::get('reports/ltv-report', [\App\Http\Controllers\Backend\CasperVPN\Reports\LTVReportController::class, 'index'])->name('admin.reports.ltv-report');
Route::get('reports/ltv-subscription-report', [\App\Http\Controllers\Backend\CasperVPN\Reports\LTVReportController::class, 'ltv_subscription'])->name('admin.reports.ltv-subscription-report');
Route::get('reports/ltv-country-report', [\App\Http\Controllers\Backend\CasperVPN\Reports\LTVReportController::class, 'ltv_country'])->name('admin.reports.ltv-country-report');
Route::get('reports/ltv-subscription-all-time-report', [\App\Http\Controllers\Backend\CasperVPN\Reports\LTVReportController::class, 'ltv_subscription_all_time'])->name('admin.reports.ltv-country-report-all-time');
Route::get('reports/ltv-subscription-all-time-report-projected', [\App\Http\Controllers\Backend\CasperVPN\Reports\LTVReportController::class, 'ltv_subscription_all_time_projected'])->name('admin.reports.ltv-country-report-all-time-projected');
Route::get('reports/ltv-subscription-all-time-report-store', [\App\Http\Controllers\Backend\CasperVPN\Reports\LTVReportController::class, 'ltv_subscription_all_time_store'])->name('admin.reports.ltv-country-report-all-time-store');
Route::get('reports/ltv-subscription-all-time-report-intercom-cross', [\App\Http\Controllers\Backend\CasperVPN\Reports\LTVReportController::class, 'ltv_subscription_all_time_intercom_cross'])->name('admin.reports.ltv-country-report-all-time-intercom-cross');


// For Reports
