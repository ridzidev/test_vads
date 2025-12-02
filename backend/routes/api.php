<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Step 2 & 3 - Transaction API
Route::post('/get-token', [ApiController::class, 'getToken']);
Route::post('/get-data', [ApiController::class, 'getData']);

// Step 4 & 5 - Chat API
Route::post('/register', [ChatController::class, 'registerCustomer']);
Route::get('/customers', [ChatController::class, 'getCustomers']);
Route::get('/chat-history/{sessionId}', [ChatController::class, 'getChatHistory']);
Route::post('/chat-message', [ChatController::class, 'saveChatMessage']);
Route::get('/sd-agents', [ChatController::class, 'getSDAgents']);
Route::get('/health', [ChatController::class, 'health']);