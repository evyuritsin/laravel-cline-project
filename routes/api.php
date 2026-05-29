<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GeolocationController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\PdfController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Move-in/out Inspection App API
|
*/

// Public routes - SMS OTP
Route::post('/auth/request-otp', [AuthController::class, 'requestOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);

// Public routes - Telegram (kept for backward compatibility)
Route::post('/auth/telegram', [AuthController::class, 'telegramLogin']);

// Geolocation (public for now)
Route::get('/geocode', [GeolocationController::class, 'geocode']);
Route::get('/reverse-geocode', [GeolocationController::class, 'reverseGeocode']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // User
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    
    // Inspections
    Route::get('/inspections', [InspectionController::class, 'index']);
    Route::post('/inspections', [InspectionController::class, 'store']);
    Route::get('/inspections/{id}', [InspectionController::class, 'show']);
    Route::put('/inspections/{id}', [InspectionController::class, 'update']);
    Route::delete('/inspections/{id}', [InspectionController::class, 'destroy']);
    
    // Rooms
    Route::post('/inspections/{inspectionId}/rooms', [RoomController::class, 'store']);
    Route::put('/rooms/{id}', [RoomController::class, 'update']);
    Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);
    
    // Photos
    Route::post('/rooms/{roomId}/photos', [PhotoController::class, 'store']);
    Route::delete('/photos/{id}', [PhotoController::class, 'destroy']);
    
    // PDF
    Route::get('/inspections/{inspectionId}/pdf', [PdfController::class, 'generate']);
    Route::post('/inspections/{inspectionId}/send', [PdfController::class, 'send']);
});