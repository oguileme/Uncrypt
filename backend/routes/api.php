<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\TypeEncryptonController;
use App\Http\Controllers\ChallangeUserController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('/type-encryption', TypeEncryptonController::class);

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::resource('/challenges', ChallengeController::class);

    Route::resource('/challenge-users', ChallangeUserController::class)->only(['index', 'show', 'store', 'update']);

    Route::post(
        '/challenge-users/{challengeUser}/attempt',
        [ChallangeUserController::class, 'attempt']
    )->name('challenge-user.attempt');

    Route::get('/user/metrics', [UserController::class, 'getUserMetrics'])->name('user.metrics');

    Route::get('challenge/recommendations', [ChallengeController::class, 'getChallengeRecommendations'])->name('challenge.recommendations');
});