<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\SampleController;
use App\Http\Controllers\FeHomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('quotes/{customer_id}',[ QuoteController::class,'api_get_quote_list'])->name('api_get_quote_list');
Route::get('quotes/{customer_id}/{qb_estimate_id}/download',[ QuoteController::class,'api_download_quote_pdf'])->name('api_download_quote_pdf');
Route::post('quote/add',[ QuoteController::class,'api_add_quote'])->name('api_add_quote');
Route::get('samples/{customer_id}',[ SampleController::class,'api_get_sample_list'])->name('api_get_sample_list');
Route::get('samples-docs/{sequence_num}',[ SampleController::class,'api_get_sample_doc_list'])->name('api_get_sample_doc_list');

Route::post('shopify_webhook',[ FeHomeController::class,'shopify_webhook']);
