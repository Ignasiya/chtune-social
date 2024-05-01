<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CommentController;
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
    Route::post('/user/follow/{user}', UserController::class)->name('user.follow');

    // Профиль пользователя
    Route::prefix('/profile')->controller(ProfileController::class)->group(function () {

        Route::post('/update-images', 'updateImage')->name('profile.updateImage');

        Route::patch('/', 'update')->name('profile.update');

        Route::delete('/', 'destroy')->name('profile.destroy');
    });

    // Записи
    Route::prefix('/post')->controller(PostController::class)->group(function () {

        Route::get('/{post}', 'view')->name('post.view');

        Route::post('/', 'store')->name('post.create');

        Route::put('/{post}', 'update')->name('post.update');

        Route::delete('/{post}', 'destroy')->name('post.destroy');

        Route::get('/download/{attachment}', 'downloadAttachment')->name('post.download');

        Route::post('/{post}/reaction', 'postReaction')->name('post.reaction');

        Route::post('/fetch-url-preview', 'fetchUrlPreview')->name('post.fetchUrlPreview');

        Route::post('/{post}/pin', 'pinUnpin')->name('post.pinUnpin');
    });

    // Комментарии
    Route::prefix('/comment')->controller(CommentController::class)->group(function () {

        Route::post('/{post}/comment', 'store')->name('post.comment.create');

        Route::delete('/{comment}', 'destroy')->name('comment.delete');

        Route::put('/{comment}', 'update')->name('comment.update');

        Route::post('/{comment}/reaction', 'commentReaction')->name('comment.reaction');
    });

    // Группы
    Route::prefix('/group')->controller(GroupController::class)->group(function () {

        Route::post('/', 'store')->name('group.create');

        Route::put('/{group:slug}', 'update')->name('group.update');

        Route::post('/update-images/{group:slug}', 'updateImage')->name('group.updateImage');

        Route::post('/invite/{group:slug}', 'inviteUsers')->name('group.inviteUsers');

        Route::post('/join/{group:slug}', 'join')->name('group.join');

        Route::post('/approve-request/{group:slug}', 'approveRequest')->name('group.approveRequest');

        Route::post('/change-role/{group:slug}', 'changeRole')->name('group.changeRole');

        Route::delete('/remove-user/{group:slug}', 'removeUser')->name('group.removeUser');
    });

    // Поиск
    Route::prefix('/search')->controller(SearchController::class)->group(function () {

        Route::get('/global/{search?}', 'search')->name('search');

        Route::get('/followers/{search?}', 'searchFollowers')->name('search.followers');

        Route::get('/followings/{search?}', 'searchFollowings')->name('search.followings');

        Route::get('/{group:slug}/{search?}', 'searchUsersInGroup')->name('search.usersGroup');
    });

    // Уведомления
    Route::prefix('/notification')->controller(NotificationController::class)->group(function () {

        Route::get('/', 'show')->name('notification.show');

        Route::patch('/{id?}', 'update')->name('notification.update');
    });
});

require __DIR__ . '/auth.php';
