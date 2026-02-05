<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return redirect()->route('elus.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public JSON feed for events (read-only) — intentionally public so dashboard calendars can load without auth
Route::get('/events/json', [\App\Http\Controllers\EventController::class, 'json'])->name('events.json');

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

    Route::get('/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');

    Route::get('/library', [\App\Http\Controllers\LibraryController::class, 'index'])->name('library.index');
});

// Espace Élus routes (requires authenticated user with élu or admin role)
Route::prefix('elus')->name('elus.')->middleware(['auth', 'elu'])->group(function () {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Elus\DashboardController::class, 'index'])->name('dashboard');

    // Instances (Comités, Bureaux, Commissions)
    Route::resource('instances', \App\Http\Controllers\Elus\InstanceController::class);

    // Projects
    Route::get('/projects/geojson', [\App\Http\Controllers\Elus\ProjectController::class, 'geojson'])->name('projects.geojson');
    Route::resource('projects', \App\Http\Controllers\Elus\ProjectController::class);

    // Reunions
    Route::get('/reunions/calendar', [\App\Http\Controllers\Elus\ReunionController::class, 'calendar'])->name('reunions.calendar');
    Route::get('/reunions/json', [\App\Http\Controllers\Elus\ReunionController::class, 'json'])->name('reunions.json');
    Route::resource('reunions', \App\Http\Controllers\Elus\ReunionController::class);

    // Documents / Library
    Route::get('/documents', [\App\Http\Controllers\Elus\DocumentController::class, 'index'])->name('documents.index');
    Route::middleware('can:admin')->group(function () {
        Route::get('/documents/create', [\App\Http\Controllers\Elus\DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [\App\Http\Controllers\Elus\DocumentController::class, 'store'])->name('documents.store');
    });

    // Admin section (only for admins within Espace Élus)
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Elus\AdminController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\Elus\AdminController::class, 'users'])->name('users');
        Route::post('/users', [\App\Http\Controllers\Elus\AdminController::class, 'storeElu'])->name('users.store');
        Route::patch('/users/{user}/toggle-elu', [\App\Http\Controllers\Elus\AdminController::class, 'toggleElu'])->name('users.toggle-elu');
        Route::patch('/users/{user}/territory', [\App\Http\Controllers\Elus\AdminController::class, 'updateTerritory'])->name('users.territory');
    });
});

require __DIR__.'/auth.php';
