<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])
    ->name('home');

Route::get('/activity', [ActivityController::class, 'index'])->middleware(['auth', 'verified'])
    ->name('activity');

Route::get('/u/{user:username}', [ProfileController::class, 'index'])
    ->name('profile');

Route::get('/g/{group:slug}', [GroupController::class, 'profile'])
    ->name('group.profile');

Route::get('/group/approve-invitation/{token}', [GroupController::class, 'approveInvitation'])
    ->name('group.approveInvitation');

Route::middleware('auth')->group(function () {

    // Пользователь
    Route::post('/user/follow/{user}', [UserController::class, 'followUser'])
        ->name('user.follow');

    // Профиль пользователя
    Route::prefix('/profile')->group(function () {
        Route::post('/update-images', [ProfileController::class, 'updateImage'])
            ->name('profile.updateImage');

        Route::patch('/', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::delete('/', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

    // Записи
    Route::prefix('/post')->group(function () {
        Route::get('/{post}', [PostController::class, 'view'])
            ->name('post.view');

        Route::post('/', [PostController::class, 'store'])
            ->name('post.create');

        Route::put('/{post}', [PostController::class, 'update'])
            ->name('post.update');

        Route::delete('/{post}', [PostController::class, 'destroy'])
            ->name('post.destroy');

        Route::get('/download/{attachment}', [PostController::class, 'downloadAttachment'])
            ->name('post.download');

        Route::post('/{post}/reaction', [PostController::class, 'postReaction'])
            ->name('post.reaction');

        Route::post('/{post}/comment', [PostController::class, 'createComment'])
            ->name('post.comment.create');

        Route::post('/fetch-url-preview', [PostController::class, 'fetchUrlPreview'])
            ->name('post.fetchUrlPreview');

        Route::post('/{post}/pin', [PostController::class, 'pinUnpin'])
            ->name('post.pinUnpin');
    });

    // Комментарии
    Route::prefix('/comment')->group(function () {
        Route::delete('/{comment}', [PostController::class, 'deleteComment'])
            ->name('comment.delete');

        Route::put('/{comment}', [PostController::class, 'updateComment'])
            ->name('comment.update');

        Route::post('/{comment}/reaction', [PostController::class, 'commentReaction'])
            ->name('comment.reaction');
    });

    // Группы
    Route::prefix('/group')->group(function () {
        Route::post('/', [GroupController::class, 'store'])
            ->name('group.create');

        Route::put('/{group:slug}', [GroupController::class, 'update'])
            ->name('group.update');

        Route::post('/update-images/{group:slug}', [GroupController::class, 'updateImage'])
            ->name('group.updateImage');

        Route::post('/invite/{group:slug}', [GroupController::class, 'inviteUsers'])
            ->name('group.inviteUsers');

        Route::post('/join/{group:slug}', [GroupController::class, 'join'])
            ->name('group.join');

        Route::post('/approve-request/{group:slug}', [GroupController::class, 'approveRequest'])
            ->name('group.approveRequest');

        Route::post('/change-role/{group:slug}', [GroupController::class, 'changeRole'])
            ->name('group.changeRole');

        Route::delete('/remove-user/{group:slug}', [GroupController::class, 'removeUser'])
            ->name('group.removeUser');
    });

    // Поиск
    Route::prefix('/search')->group(function () {
        Route::get('/global/{search?}', [SearchController::class, 'search'])
            ->name('search');

        Route::get('/followers/{search?}', [SearchController::class, 'searchFollowers'])
            ->name('search.followers');

        Route::get('/followings/{search?}', [SearchController::class, 'searchFollowings'])
            ->name('search.followings');

        Route::get('/{group:slug}/{search?}', [SearchController::class, 'searchUsersInGroup'])
            ->name('search.usersGroup');
    });

    // Уведомления
    Route::prefix('/notification')->group(function () {
        Route::get('/', [NotificationController::class, 'show'])
            ->name('notification.show');

        Route::patch('/', [NotificationController::class, 'update'])
            ->name('notification.update');
    });
});

require __DIR__ . '/auth.php';
