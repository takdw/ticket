<?php

use App\Http\Controllers\PublishTicketsContorller;
use App\Http\Controllers\TicketApproveController;
use App\Http\Controllers\TicketSellController;
use App\Http\Controllers\WalletDepositController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/tickets/{ticket}/buy', [TicketSellController::class, 'store']);
Route::post('/wallet/deposit', [WalletDepositController::class, 'store']);

Route::middleware('auth')->group(function () {
    Route::post('/tickets/{ticket}/publish', [PublishTicketsContorller::class, 'store']);
    Route::post('/tickets/{ticket}/approve', [TicketApproveController::class, 'store']);
});

