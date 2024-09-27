<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\Api\ApiMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;




Route::get('/test', [ApiController::class, 'test']);
Route::get('/getUsers', [ApiController::class, 'getUsers']);
Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);
Route::get('/logout', [ApiController::class, 'logout']);
Route::post('/getUser', [ApiController::class, 'getUser']);

Route::post('/getQuestion', [ApiController::class, 'getQuestion']);

Route::post('/qImg', [ApiController::class, 'qImg']);
Route::post('/solutions', [ApiController::class, 'solutions']);
Route::post('/solution_filter', [ApiController::class, 'solution_filter']);
Route::post('/add_filter', [ApiController::class, 'add_filter']);
Route::post('/add_question', [ApiController::class, 'add_question']);
Route::post('/welcomeMessage', [ApiController::class, 'welcomeMessage']);
Route::post('/getFilteredData', [ApiController::class, 'getFilteredData']);
Route::post('/userSolution', [ApiController::class, 'userSolution']);
Route::post('/getSolutionBYID', [ApiController::class, 'getSolutionBYID']);
Route::post('/add_solutions', [ApiController::class, 'add_solutions']);
Route::post('/remove_solutions', [ApiController::class, 'remove_solutions']);



Route::any('/clear', function() {
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return 'Cache Cleared';
});
