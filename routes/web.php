<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserEventController;
use App\Http\Controllers\EventMapController;
use App\Http\Controllers\UserPreferencesController;

/*
|--------------------------------------------------------------------------
| Rotte Web
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/events');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/events-map', [EventMapController::class, 'index'])->name('events.map');
Route::get('/api/events-map', [EventMapController::class, 'getEventsJson'])->name('api.events.map');

Route::middleware('auth')->group(function () {
    Route::get('/my-events', [UserEventController::class, 'myEvents'])->name('my-events');
    Route::post('/events/{event}/register', [UserEventController::class, 'register'])->name('events.register');
    Route::post('/events/{event}/unregister', [UserEventController::class, 'unregister'])->name('events.unregister');

    Route::get('/preferences', [UserPreferencesController::class, 'index'])->name('preferences.index');
    Route::post('/preferences', [UserPreferencesController::class, 'update'])->name('preferences.update');
    Route::get('/notifications', [UserPreferencesController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [UserPreferencesController::class, 'markAsRead'])->name('notifications.read');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [EventController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/events/export', [EventController::class, 'exportEvents'])->name('events.export');
        Route::get('/events/{event}/export-participants', [EventController::class, 'exportParticipants'])->name('events.export-participants');
        Route::resource('events', EventController::class)->except(['index', 'show']);
    });
});
