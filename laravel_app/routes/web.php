<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\RedirectIfAuthenticated;

use App\Http\Controllers\FeHomeController;
use App\Http\Controllers\QBController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\SampleController;

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

Route::get('/qb_auth',[ QBController::class,'qb_auth'])->name('qb_auth');
Route::get('/qb_callback',[ QBController::class,'qb_callback'])->name('qb_callback');
Route::get('/cronjob_qb_refresh_token',[ QBController::class,'cronjob_qb_refresh_token'])->name('cronjob_qb_refresh_token');

Route::group(['prefix' => 'admin'], function() {
    Route::get('/',[ HomeController::class,'admin_login']);
    Route::get('login',[ HomeController::class,'admin_login'])->name('admin_login');
    Route::post('admin_login_post',[ HomeController::class,'admin_login_post'])->name('admin_login_post');

    Route::middleware([RedirectIfAuthenticated::class])->group(function () {
        Route::get('home',[ HomeController::class,'admin_home'])->name('admin_home');
        Route::get('admin_logout',[ HomeController::class,'admin_logout'])->name('admin_logout');

        Route::get('samples',[ SampleController::class,'list_sample'])->name('list_sample');
        Route::post('list_sample_post',[ SampleController::class,'list_sample_post'])->name('list_sample_post');
        Route::post('list_sample_docs_post',[ SampleController::class,'list_sample_docs_post'])->name('list_sample_docs_post');

    });
});
