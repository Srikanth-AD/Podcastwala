<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Podcast routes (auth required via controller middleware)
Route::get('/', [PodcastController::class, 'index']);
Route::get('podcast/player', [PodcastController::class, 'index'])->name('podcast.player');
Route::get('podcast/manage', [PodcastController::class, 'manage'])->name('podcast.manage');
Route::get('podcast/favorites', [PodcastController::class, 'favorites'])->name('podcast.favorites');
Route::get('podcast/settings', [PodcastController::class, 'settings'])->name('podcast.settings');
Route::post('podcast/add', [PodcastController::class, 'add'])->name('podcast.add');
Route::post('podcast/delete', [PodcastController::class, 'delete'])->name('podcast.delete');
Route::get('podcast/auto-update', [PodcastController::class, 'autoUpdate'])->name('podcast.auto-update');

// Item routes
Route::get('item/search', [ItemController::class, 'search'])->name('item.search');
Route::post('item/mark-as-read', [ItemController::class, 'markAsRead'])->name('item.mark-as-read');
Route::post('item/mark-as-favorite', [ItemController::class, 'markAsFavorite'])->name('item.mark-as-favorite');
Route::post('item/mark-all-prev-read', [ItemController::class, 'markAllPrevAsRead'])->name('item.mark-all-prev-read');

// Authentication routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
