<?php

use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\PublishTicketsContorller;
use App\Http\Controllers\TicketApproveController;
use App\Http\Controllers\TicketSellController;
use App\Http\Controllers\UserActivationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VendorActivationController;
use App\Http\Controllers\VendorAuthController;
use App\Http\Controllers\VendorVerifyController;
use App\Http\Controllers\VendorsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tickets/{ticket}/buy', [TicketSellController::class, 'store']);
Route::post('/wallet/deposit', [WalletDepositController::class, 'store']);

Route::middleware('auth')->group(function () {
    Route::post('/tickets/{ticket}/publish', [PublishTicketsContorller::class, 'store']);
    Route::post('/tickets/{ticket}/approve', [TicketApproveController::class, 'store']);
    Route::post('/vendors/{vendor}/approve', [VendorVerifyController::class, 'store']);
});

Route::post('/users', [UsersController::class, 'store']);
Route::post('/vendors', [VendorsController::class, 'store']);
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/vendors/login', [VendorAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vendor', function () {
        return response()->json(auth()->user(), 200);
    });
    
    Route::patch('/user', [UserController::class, 'update']);
    Route::patch('/vendor', [VendorsController::class, 'update']);
    Route::post('/users/{user}/deactivate', [UserActivationController::class, 'store']);
    Route::post('/vendors/{vendor}/deactivate', [VendorActivationController::class, 'store']);
});
