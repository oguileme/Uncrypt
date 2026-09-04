<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\TypeEncryptonController;
use App\Http\Controllers\ChallangeUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AchievementUserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('/achievement', AchievementController::class);
Route::resource('/achievement-progress', AchievementUserController::class);




// leitura dos tipos de cifra e publica (usada na landing e na listagem)
Route::get('/type-encryption', [TypeEncryptonController::class, 'index']);
Route::get('/type-encryption/{typeEncrypton}', [TypeEncryptonController::class, 'show']);

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    // mutacoes dos tipos de cifra exigem autenticacao e rate limit
    Route::post('/type-encryption', [TypeEncryptonController::class, 'store'])->middleware('throttle:writes');
    Route::put('/type-encryption/{typeEncrypton}', [TypeEncryptonController::class, 'update'])->middleware('throttle:writes');
    Route::delete('/type-encryption/{typeEncrypton}', [TypeEncryptonController::class, 'destroy'])->middleware('throttle:writes');

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:writes');

    Route::resource('/challenges', ChallengeController::class);

    Route::resource('/challenge-users', ChallangeUserController::class)->only(['index', 'show', 'store', 'update']);

    Route::post(
        '/challenge-users/{challengeUser}/attempt',
        [ChallangeUserController::class, 'attempt']
    )->middleware('throttle:attempts')->name('challenge-user.attempt');

    Route::get('/user/metrics', [UserController::class, 'getUserMetrics'])->name('user.metrics');

    Route::get('/user/recent-activity', [UserController::class, 'getRecentActivity'])->name('user.recent-activity');

    Route::get('challenge/recommendations', [ChallengeController::class, 'getChallengeRecommendations'])->name('challenge.recommendations');
});