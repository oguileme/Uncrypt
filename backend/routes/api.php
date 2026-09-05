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




// leitura dos tipos de cifra e publica (usada na landing e na listagem)
Route::get('/type-encryption', [TypeEncryptonController::class, 'index'])->middleware('cache.public');
Route::get('/type-encryption/{typeEncrypton}', [TypeEncryptonController::class, 'show'])->middleware('cache.public');

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    // mutacoes dos tipos de cifra exigem autenticacao, admin e rate limit
    Route::post('/type-encryption', [TypeEncryptonController::class, 'store'])->middleware(['throttle:writes', 'admin']);
    Route::put('/type-encryption/{typeEncrypton}', [TypeEncryptonController::class, 'update'])->middleware(['throttle:writes', 'admin']);
    Route::delete('/type-encryption/{typeEncrypton}', [TypeEncryptonController::class, 'destroy'])->middleware(['throttle:writes', 'admin']);

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:writes');

    // writes de desafios exigem admin (leitura fica nos GETs abaixo)
    Route::resource('/challenges', ChallengeController::class)->except(['index', 'show'])->middleware('admin');

    // GETs de leitura com cache HTTP no browser (payload identico entre usuarios, escopo private)
    Route::get('/challenges', [ChallengeController::class, 'index'])->middleware('cache.public:1800,private');
    Route::get('/challenges/{challenge}', [ChallengeController::class, 'show'])->middleware('cache.public:1800,private');

    Route::resource('/challenge-users', ChallangeUserController::class)->only(['index', 'show', 'store', 'update']);

    // conquistas (conteudo/definicoes): leitura autenticada, escrita apenas admin
    Route::get('/achievement', [AchievementController::class, 'index']);
    Route::get('/achievement/{achievement}', [AchievementController::class, 'show']);
    Route::post('/achievement', [AchievementController::class, 'store'])->middleware('admin');
    Route::put('/achievement/{achievement}', [AchievementController::class, 'update'])->middleware('admin');
    Route::delete('/achievement/{achievement}', [AchievementController::class, 'destroy'])->middleware('admin');

    // progresso das conquistas: sempre do usuario autenticado
    Route::get('/achievement-progress', [AchievementUserController::class, 'index']);
    Route::post('/achievement-progress', [AchievementUserController::class, 'store']);
    Route::get('/achievement-progress/{achievementUser}', [AchievementUserController::class, 'show']);
    Route::put('/achievement-progress/{achievementUser}', [AchievementUserController::class, 'update']);
    Route::delete('/achievement-progress/{achievementUser}', [AchievementUserController::class, 'destroy']);

    Route::post(
        '/challenge-users/{challengeUser}/attempt',
        [ChallangeUserController::class, 'attempt']
    )->middleware('throttle:attempts')->name('challenge-user.attempt');

    Route::get('/user/metrics', [UserController::class, 'getUserMetrics'])->name('user.metrics');

    Route::get('/user/recent-activity', [UserController::class, 'getRecentActivity'])->name('user.recent-activity');

    Route::get('challenge/recommendations', [ChallengeController::class, 'getChallengeRecommendations'])->name('challenge.recommendations');
});