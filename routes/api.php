<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClineController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Laravel Cline API',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Cline CLI API endpoints
Route::prefix('cline')->name('api.cline.')->group(function () {
    
    // Check Cline CLI availability
    Route::get('/check', [ClineController::class, 'check'])->name('check');
    
    // Get allowed commands
    Route::get('/commands', [ClineController::class, 'allowedCommands'])->name('commands');

    // Execute a Cline AI task (synchronous or asynchronous)
    Route::post('/execute', [ClineController::class, 'execute'])->name('execute');

    // Execute shell commands
    Route::post('/command', [ClineController::class, 'command'])->name('command');
    
    // Execute Artisan commands
    Route::post('/artisan', [ClineController::class, 'artisan'])->name('artisan');
    
    // Execute Composer commands
    Route::post('/composer', [ClineController::class, 'composer'])->name('composer');
    
    // Execute NPM commands
    Route::post('/npm', [ClineController::class, 'npm'])->name('npm');
    
    // Execute Git commands
    Route::post('/git', [ClineController::class, 'git'])->name('git');

    // Get execution history
    Route::get('/history', [ClineController::class, 'history'])->name('history');

    // Get execution status
    Route::get('/status/{taskId}', [ClineController::class, 'status'])->name('status');

    // Get specific execution details
    Route::get('/execution/{taskId}', [ClineController::class, 'show'])->name('show');

    // Delete execution record
    Route::delete('/execution/{taskId}', [ClineController::class, 'destroy'])->name('destroy');

    // Configure Cline settings
    Route::post('/configure', [ClineController::class, 'configure'])->name('configure');
});
