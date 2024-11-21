<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\User;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// User kyc route //

Route::post("register", [UserController::class, "register"]);
Route::middleware('auth:sanctum')->post("login", [UserController::class, "login"]);
Route::middleware('auth:sanctum')->delete('/user/{id}', [UserController::class, 'delete']);

