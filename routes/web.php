<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RSVPController;
use Illuminate\Support\Facades\Route;

// Profile Section

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Application Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/store', [EventController::class, 'store'])->name('events.store');
        Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::patch('/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');

            Route::prefix('{event}/guests')->group(function () {
                Route::get('/', [GuestController::class, 'index'])->name('guests.index');
                Route::get('/create', [GuestController::class, 'create'])->name('guests.create');
                Route::post('/', [GuestController::class, 'store'])->name('guests.store');
                Route::get('/export', [GuestController::class, 'export'])->name('guests.export');
                // Route::get('/{event}', [EventController::class, 'show'])->name('events.show'); // Not needed
                Route::get('/{guest}/edit', [GuestController::class, 'edit'])->name('guests.edit');
            Route::patch('/{guest}', [GuestController::class, 'update'])->name('guests.update');
            Route::delete('/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

            Route::get('/import', [ImportController::class, 'import'])->name('guests.import');
            Route::post('/import', [ImportController::class, 'store'])->name('guests.import.store');
            Route::get('/import/sample', [ImportController::class, 'sample'])->name('guests.import.sample');
        });
    });

});

// Public Reservation Routes
Route::get('/invite/{code}', [RSVPController::class, 'show'])->name('rsvp.show');
Route::post('/invite/{code}', [RSVPController::class, 'respond'])->name('rsvp.respond');

require __DIR__.'/auth.php';
