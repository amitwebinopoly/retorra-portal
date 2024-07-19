<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\RedirectIfAuthenticated;

use App\Http\Controllers\FeHomeController;
use App\Http\Controllers\QBController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\SampleController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\PHPMailer\MailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[ FeHomeController::class,'fe_home'])->name('fe_home');

Route::get('/test_mail',[ MailController::class,'test_mail']);

Route::get('/qb_auth',[ QBController::class,'qb_auth'])->name('qb_auth');
Route::get('/qb_callback',[ QBController::class,'qb_callback'])->name('qb_callback');
Route::get('/sync_qb_customer_types',[ QBController::class,'sync_qb_customer_types'])->name('sync_qb_customer_types');
Route::get('/sync_qb_customers',[ QBController::class,'sync_qb_customers'])->name('sync_qb_customers');

Route::group(['prefix' => 'admin'], function() {
    Route::get('/',[ HomeController::class,'admin_login']);
    Route::get('login',[ HomeController::class,'admin_login'])->name('admin_login');
    Route::post('admin_login_post',[ HomeController::class,'admin_login_post'])->name('admin_login_post');

    Route::middleware([RedirectIfAuthenticated::class])->group(function () {
        Route::get('home',[ HomeController::class,'admin_home'])->name('admin_home');
        Route::get('admin_logout',[ HomeController::class,'admin_logout'])->name('admin_logout');

        Route::get('users',[ UsersController::class,'list_user'])->name('list_user');
        Route::post('list_user_post',[ UsersController::class,'list_user_post'])->name('list_user_post');
        Route::get('users/new',[ UsersController::class,'add_user'])->name('add_user');
        Route::post('add_user_post',[ UsersController::class,'add_user_post'])->name('add_user_post');
        Route::get('users/{id}',[ UsersController::class,'edit_user'])->name('edit_user');
        Route::post('edit_user_post',[ UsersController::class,'edit_user_post'])->name('edit_user_post');
        Route::post('edit_user_password_post',[ UsersController::class,'edit_user_password_post'])->name('edit_user_password_post');

        Route::get('samples',[ SampleController::class,'list_sample'])->name('list_sample');
        Route::post('list_sample_post',[ SampleController::class,'list_sample_post'])->name('list_sample_post');
        Route::post('list_sample_docs_post',[ SampleController::class,'list_sample_docs_post'])->name('list_sample_docs_post');

        Route::get('quotes',[ QuoteController::class,'list_quote'])->name('list_quote');
        Route::post('list_quote_post',[ QuoteController::class,'list_quote_post'])->name('list_quote_post');

        Route::get('orders',[ QuoteController::class,'list_order'])->name('list_order');
        Route::post('list_order_post',[ QuoteController::class,'list_order_post'])->name('list_order_post');

        Route::get('settings',[ HomeController::class,'settings'])->name('settings');
        Route::post('settings_post',[ HomeController::class,'settings_post'])->name('settings_post');
    });
});

//cronjobs
Route::get('/cronjob_qb_refresh_token',[ QBController::class,'cronjob_qb_refresh_token'])->name('cronjob_qb_refresh_token');
Route::get('/cronjob_check_estimate_status',[ QuoteController::class,'cronjob_check_estimate_status'])->name('cronjob_check_estimate_status');