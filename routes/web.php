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

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function () {
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DocumentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\DocumentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])->name('store');
        Route::get('/{document}/edit', [\App\Http\Controllers\Admin\DocumentController::class, 'edit'])->name('edit');
        Route::patch('/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProjectController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProjectController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProjectController::class, 'store'])->name('store');
        Route::get('/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [\App\Http\Controllers\Admin\ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('reunions')->name('reunions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReunionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ReunionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ReunionController::class, 'store'])->name('store');
        Route::get('/{reunion}/edit', [\App\Http\Controllers\Admin\ReunionController::class, 'edit'])->name('edit');
        Route::put('/{reunion}', [\App\Http\Controllers\Admin\ReunionController::class, 'update'])->name('update');
        Route::delete('/{reunion}', [\App\Http\Controllers\Admin\ReunionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('actualites')->name('actualites.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ActualiteController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ActualiteController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ActualiteController::class, 'store'])->name('store');
        Route::get('/{actualite}/edit', [\App\Http\Controllers\Admin\ActualiteController::class, 'edit'])->name('edit');
        Route::patch('/{actualite}', [\App\Http\Controllers\Admin\ActualiteController::class, 'update'])->name('update');
        Route::delete('/{actualite}', [\App\Http\Controllers\Admin\ActualiteController::class, 'destroy'])->name('destroy');
    });
});

// Public/Authenticated routes for documents (download, embed, info & library)
Route::middleware('auth')->group(function () {
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download')->middleware('throttle:30,1');
    Route::get('/documents/{document}/embed', [\App\Http\Controllers\Admin\DocumentController::class, 'embed'])->name('documents.embed')->middleware('throttle:60,1');
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
    Route::post('/onboarding-complete', [\App\Http\Controllers\Elus\DashboardController::class, 'onboardingComplete'])->name('onboarding.complete');
    Route::get('/weather-by-coords', [\App\Http\Controllers\Elus\DashboardController::class, 'weatherByCoords'])->name('weather.by-coords');

    // Instances (Comités, Bureaux, Commissions)
    Route::resource('instances', \App\Http\Controllers\Elus\InstanceController::class)->only(['index', 'show']);

    // Projects
    Route::get('/projects/geojson', [\App\Http\Controllers\Elus\ProjectController::class, 'geojson'])->name('projects.geojson');
    Route::resource('projects', \App\Http\Controllers\Elus\ProjectController::class);

    // Reunions
    Route::get('/reunions/calendar', [\App\Http\Controllers\Elus\ReunionController::class, 'calendar'])->name('reunions.calendar');
    Route::get('/reunions/json', [\App\Http\Controllers\Elus\ReunionController::class, 'json'])->name('reunions.json');
    Route::post('/reunions/toggle-calendar', [\App\Http\Controllers\Elus\ReunionController::class, 'toggleCalendar'])->name('reunions.toggle-calendar');
    Route::resource('reunions', \App\Http\Controllers\Elus\ReunionController::class);

    // Documents / Library
    Route::get('/documents', [\App\Http\Controllers\Elus\DocumentController::class, 'index'])->name('documents.index');
    Route::middleware('can:admin')->group(function () {
        Route::get('/documents/create', [\App\Http\Controllers\Elus\DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [\App\Http\Controllers\Elus\DocumentController::class, 'store'])->name('documents.store');
    });

    // Forum (remplace l'ancien module Collaboratif)
    Route::get('/forum', [\App\Http\Controllers\Elus\ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/creer', [\App\Http\Controllers\Elus\ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [\App\Http\Controllers\Elus\ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{forum_thread}', [\App\Http\Controllers\Elus\ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/{forum_thread}/posts', [\App\Http\Controllers\Elus\ForumController::class, 'storePost'])->name('forum.posts.store');

    // Actualités
    Route::get('/actualites', [\App\Http\Controllers\Elus\ActualiteController::class, 'index'])->name('actualites.index');
    Route::get('/actualites/{actualite}', [\App\Http\Controllers\Elus\ActualiteController::class, 'show'])->name('actualites.show');

    // Admin section (only for admins within Espace Élus)
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Elus\AdminController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\Elus\AdminController::class, 'users'])->name('users');
        Route::post('/users', [\App\Http\Controllers\Elus\AdminController::class, 'storeElu'])->name('users.store');
        Route::get('/users/import', [\App\Http\Controllers\Elus\AdminController::class, 'importForm'])->name('users.import.form');
        Route::post('/users/import', [\App\Http\Controllers\Elus\AdminController::class, 'importCsv'])->name('users.import');
        Route::patch('/users/{user}/toggle-elu', [\App\Http\Controllers\Elus\AdminController::class, 'toggleElu'])->name('users.toggle-elu');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Elus\AdminController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
