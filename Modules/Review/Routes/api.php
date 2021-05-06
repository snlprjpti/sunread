<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['api']], function () {
    //ADMIN CATEGORY ROUTES
    Route::group(['prefix'=>'admin', 'as' => 'admin.', 'middleware' => ['admin', 'language']], function () {
        Route::resource('reviews', ReviewController::class)->except(['create', 'edit']);
        Route::resource('review_votes', ReviewVoteController::class)->except(['create', 'edit']);
        Route::resource('review_replies', ReviewReplyController::class)->except(['create', 'edit']);
        Route::get('pending/reviews', [Modules\Review\Http\Controllers\ReviewController::class, 'pendingList'])->name('reviews.pending');
        Route::get('reviews/{id}/verify', [Modules\Review\Http\Controllers\ReviewController::class, 'verify'])->name('reviews.verify');
    });
});