<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

// Login (tidak butuh autentikasi)
Route::post('login', [AuthController::class, 'login']);


// Group route dengan middleware 
Route::middleware('auth:api')->group(function () {

    // Endpoint untuk NGAMBIL profil user yang sedang login
    Route::get('/profile', function () {
        return response()->json(auth()->user());
    });

     // Search
     Route::get('/users/search',[UserController::class, 'search']);
     // CRUD 
     Route::get('users', [UserController::class, 'index']);      // Get all users
     Route::post('users', [UserController::class, 'store']);     // Create new user
     Route::get('users/{id}', [UserController::class, 'show']);   // Get user by ID
     Route::put('users/{id}', [UserController::class, 'update']); // Update user
     Route::delete('users/{id}', [UserController::class, 'destroy']); // Delete user
    
});
