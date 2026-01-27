<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes (requires authenticated admin user)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Documents management
    Route::get('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [\App\Http\Controllers\Admin\DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/edit', [\App\Http\Controllers\Admin\DocumentController::class, 'edit'])->name('documents.edit');
    Route::patch('/documents/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('documents.destroy');

    // Document download (admin-only route kept for backwards compatibility)
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('admin.documents.download');

    // Events management
    Route::get('/events', [\App\Http\Controllers\Admin\EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [\App\Http\Controllers\Admin\EventController::class, 'create'])->name('events.create');
    Route::post('/events', [\App\Http\Controllers\Admin\EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [\App\Http\Controllers\Admin\EventController::class, 'edit'])->name('events.edit');
    Route::patch('/events/{event}', [\App\Http\Controllers\Admin\EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [\App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('events.destroy');
});

// Public/Authenticated routes for documents (download, embed, info & library)
Route::middleware('auth')->group(function () {
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/embed', [\App\Http\Controllers\Admin\DocumentController::class, 'embed'])->name('documents.embed');
    Route::get('/documents/{document}/info', [\App\Http\Controllers\Admin\DocumentController::class, 'info'])->name('documents.info');

    // Events (publicly viewable for authenticated users)
    Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');

    // Calendar UI + JSON feed for FullCalendar (declare before the catch-all show route)
    Route::get('/events/calendar', [\App\Http\Controllers\EventController::class, 'calendar'])->name('events.calendar');
    Route::get('/events/json', [\App\Http\Controllers\EventController::class, 'json'])->name('events.json');

    Route::get('/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');

    Route::get('/library', [\App\Http\Controllers\LibraryController::class, 'index'])->name('library.index');
});

require __DIR__.'/auth.php';
