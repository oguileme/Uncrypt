<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\TypeEncryptonController;
use App\Http\Controllers\ChallangeUserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('users', UserController::class);

Route::resource('/type-encryption', TypeEncryptonController::class);

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

    Route::resource('/challenges', ChallengeController::class);

    Route::resource('/challenge-users', ChallangeUserController::class)->only(['index', 'show', 'store', 'update']);

    Route::post(
        '/challenge-users/{challengeUser}/attempt',
        [ChallangeUserController::class, 'attempt']
    )->name('challenge-user.attempt');

    Route::get('/user/metrics', [UserController::class, 'getUserMetrics'])->name('user.metrics');
    
});