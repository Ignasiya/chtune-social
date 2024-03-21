<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])
    ->name('home');

Route::get('/u/{user:username}', [ProfileController::class, 'index'])
    ->name('profile');

Route::get('/g/{group:slug}', [GroupController::class, 'profile'])
    ->name('group.profile');

Route::middleware('auth')->group(function () {

    // профиль пользователя
    Route::post('/profile/update-images', [ProfileController::class, 'updateImage'])
        ->name('profile.updateImage');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Записи
    Route::post('/post', [PostController::class, 'store'])
        ->name('post.create');
    Route::put('/post/{post}', [PostController::class, 'update'])
        ->name('post.update');
    Route::delete('/post/{post}', [PostController::class, 'destroy'])
        ->name('post.destroy');
    Route::get('/post/download/{attachment}', [PostController::class, 'downloadAttachment'])
        ->name('post.download');
    Route::post('/post/{post}/reaction', [PostController::class, 'postReaction'])
        ->name('post.reaction');
    Route::post('/post/{post}/comment', [PostController::class, 'createComment'])
        ->name('post.comment.create');

    // Комментарии
    Route::delete('/comment/{comment}', [PostController::class, 'deleteComment'])
        ->name('comment.delete');
    Route::put('/comment/{comment}', [PostController::class, 'updateComment'])
        ->name('comment.update');
    Route::post('/comment/{comment}/reaction', [PostController::class, 'commentReaction'])
        ->name('comment.reaction');

    // Группы
    Route::post('/group', [GroupController::class, 'store'])
        ->name('group.create');
    Route::post('/group/update-images/{group:slug}', [GroupController::class, 'updateImage'])
        ->name('group.updateImage');
});

require __DIR__ . '/auth.php';
