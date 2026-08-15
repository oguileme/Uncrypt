<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\PhraseController;
use App\Http\Controllers\ChallengeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route::resource('users', UserController::class);

Route::resource('keys', KeyController::class);
Route::resource('phrases', PhraseController::class);

Route::resource('challenges', ChallengeController::class);
Route::post('challenge/attempt/{attempt}', [ChallengeController::class, 'attemptChallenge'])->name('challenge.attempt');



