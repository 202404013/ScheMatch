<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MessageController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{user}/get', [MessageController::class, 'getMessages'])->name('messages.get');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{user}', [MessageController::class, 'show'])->name('messages.show');
});

Route::resource('courses', CourseController::class)->middleware(['auth']);

Route::resource('friends', FriendController::class)->middleware(['auth']);

Route::middleware(['auth'])->group(function () {
    Route::get('availabilities/users/public', [AvailabilityController::class, 'getPublicUsers'])->name('availabilities.public-users');
    Route::get('availabilities/common-slots', [AvailabilityController::class, 'findCommonSlots'])->name('availabilities.common-slots');
    Route::post('availabilities/{id}/update-drag', [AvailabilityController::class, 'updateDrag'])->name('availabilities.updateDrag');
    Route::post('availabilities/{id}/visibility', [AvailabilityController::class, 'updateVisibility'])->name('availabilities.updateVisibility');
});

Route::resource('availabilities', AvailabilityController::class)->middleware(['auth']);