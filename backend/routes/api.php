<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\PhraseController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\TypeEncryptonController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('users', UserController::class);

Route::resource('/keys', KeyController::class);
Route::resource('/phrases', PhraseController::class);
Route::resource('/type-encryption', TypeEncryptonController::class);

Route::resource('/challenges', ChallengeController::class);
Route::post('/challenge/attempt/{attempt}', [ChallengeController::class, 'attemptChallenge'])->name('challenge.attempt');

Route::get('/test-cesar', function(){
    return App\Helpers\CipherHelper::CesarEncrypt("guilherme", 1);
});

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

    Route::resource('/challenges', ChallengeController::class);

    Route::post(
        '/challenge/attempt/{attempt}',
        [ChallengeController::class, 'attemptChallenge']
    )->name('challenge.attempt');
});