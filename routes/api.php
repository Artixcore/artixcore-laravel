<?php

use App\Http\Controllers\Api\V1\ContactController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn () => ['status' => 'ok', 'timestamp' => now()->toIso8601String()]);

    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:10,1');
});
