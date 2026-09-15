<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JavaCodeController;
use App\Http\Controllers\Api\LineCommentController; // Import the new controller
use App\Http\Controllers\CompileController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/java/run', [JavaCodeController::class, 'run']);

    // Line Comments API routes
    Route::get('/submissions/{submission}/comments', [LineCommentController::class, 'index']);
    Route::post('/submissions/{submission}/comments', [LineCommentController::class, 'store']);
    Route::patch('/comments/{lineComment}', [LineCommentController::class, 'update']);
    Route::delete('/comments/{lineComment}', [LineCommentController::class, 'destroy']);
});

Route::post('/compile', [CompileController::class, 'run']);
