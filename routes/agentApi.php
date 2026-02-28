<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClineController;
use App\Http\Controllers\Mp4Controller;

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

// MP4 File API endpoints
Route::prefix('mp4')->name('api.mp4.')->group(function () {
    
    // List all MP4 files
    Route::get('/files', [Mp4Controller::class, 'index'])->name('index');
    
    // Get MP4 file statistics
    Route::get('/stats', [Mp4Controller::class, 'stats'])->name('stats');
    
    // Get information about a specific MP4 file
    Route::get('/file/{filename}', [Mp4Controller::class, 'show'])->name('show');
    
    // Stream an MP4 file
    Route::get('/stream/{filename}', [Mp4Controller::class, 'stream'])->name('stream');
    
    // Download an MP4 file
    Route::get('/download/{filename}', [Mp4Controller::class, 'download'])->name('download');
    
    // Upload a new MP4 file
    Route::post('/upload', [Mp4Controller::class, 'upload'])->name('upload');
    
    // Delete an MP4 file
    Route::delete('/file/{filename}', [Mp4Controller::class, 'destroy'])->name('destroy');
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
