<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controller\api\CardController;
use App\Http\Controller\api\TerminalController;
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

Route::middleware(['auth:sanctum', 'admin'])->post('/user/register_admin', [UserController::class,'register_admin']);
Route::middleware(['auth:sanctum', 'admin'])->post('/user/register_terminal', [UserController::class, 'register_terminal']);
Route::middleware(['auth:sanctum', 'admin'])->post('/logout',[UserController::class, 'logout']);
Route::post('/login', [UserController::class,'login']);

/**
 * City Controller
 */

Route::get('city', [CityController::class, 'index']);

/**
 * API Route
 * 
 */
Route::middleware(['auth:sanctum','admin'])->post('/user/token', [UserController::class, 'terminal_token']);
Route::middleware(['auth:sanctum', 'admin'])->get('/user/list', [UserController::class, 'list']);

/** 
 * Admin auth
 * 
 */
Route::middleware(['auth:sanctum', 'admin'])->post('/terminal/create', [TerminalController::class,'create']);
Route::middleware(['auth:sanctum', 'admin'])->get('/terminal/list', [TerminalController::class, 'list']);
Route::middleware(['auth:sanctum', 'admin'])->post('/card/create', [CardController::class, 'create']);
Route::middleware(['auth:sanctum', 'admin'])->get('/card/list', [CardController::class, 'list']);

Route::middleware(['auth:sanctum', 'terminal'])->get('/card/balance/{id}',
[CardController::class,'balance']);
Route::middleware(['auth:sanctum', 'terminal'])->post('/card/pay',
[CardController::class,'pay']);
Route::middleware(['auth:sanctum', 'terminal'])->post('/card/deposit',
[CardController::class,'deposit']);

/**
 * Route Payment
 * 
 */

Route::post('payments/midtrans-notification', [PaymentCallbackController::class, 'receive']);

/**
 * Midtrans route 
 */
Route::post('payments/midtrans-notification', [PaymentCallbackController::class, 'midtransNotification']);