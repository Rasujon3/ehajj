<?php

use App\Modules\News\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::group(array('Module'=>'News', 'middleware' => ['web','auth','GlobalSecurity']), function () {
    Route::get('news/{id}', [NewsController::class, 'newsDisplay']);
    Route::get('newslist', [NewsController::class, 'newsList']);
    Route::get('newslist/get-news-list', [NewsController::class, 'getNewsList']);
    Route::get('newslist/edit/{id}', [NewsController::class, 'editNewsList']);
    Route::post('newslist/update/{id}', [NewsController::class, 'updateNewsList']);
});
