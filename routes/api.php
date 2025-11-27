<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Notification API routes for polling fallback
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/unread-count', function (Request $request) {
        $notifications = $request->user()->unreadNotifications()->latest()->take(10)->get();
        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    });

    Route::get('/notifications/latest', function (Request $request) {
        $notifications = $request->user()->unreadNotifications()->latest()->take(10)->get();
        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    });
});
