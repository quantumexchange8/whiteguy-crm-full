<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\UserClientController;
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

/*
|--------------------------------------------------------------------------
| CRM Routes
|--------------------------------------------------------------------------
|
    --------------------------------------------------------------------------
    | User Client Module
*/
    // Test getting all user data
    Route::get('v1/data/users-clients', [UserClientController::class, 'getUsersClients']);
    Route::get('v1/data/users-clients/sites', [UserClientController::class, 'getAllSites']);

/*
    |
    --------------------------------------------------------------------------
    
    --------------------------------------------------------------------------
    | Lead Module
*/
    Route::get('v1/data/leads/categories', [LeadController::class, 'getCategories']);

/*
    |
    --------------------------------------------------------------------------
|
|--------------------------------------------------------------------------
*/  
