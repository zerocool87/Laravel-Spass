<?php

use App\Http\Controllers\Admin\ActualiteController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ReunionController as AdminReunionController;
use App\Http\Controllers\Admin\ThematiqueController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Elus\ActualiteController as ElusActualiteController;
use App\Http\Controllers\Elus\AdminController;
use App\Http\Controllers\Elus\DashboardController;
use App\Http\Controllers\Elus\DocumentController as ElusDocumentController;
use App\Http\Controllers\Elus\ForumController;
use App\Http\Controllers\Elus\InstanceController;
use App\Http\Controllers\Elus\ProjectController as ElusProjectController;
use App\Http\Controllers\Elus\ReunionController as ElusReunionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return redirect()->route('elus.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public JSON feed for events (read-only) — intentionally public so dashboard calendars can load without auth
Route::get('/events/json', [EventController::class, 'json'])->name('events.json')->middleware('throttle:60,1');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function () {
    Route::resource('documents', DocumentController::class)->except(['show', 'download', 'embed', 'info']);
    Route::resource('projects', AdminProjectController::class);
    Route::resource('reunions', AdminReunionController::class);
    Route::resource('actualites', ActualiteController::class);
    Route::resource('thematiques', ThematiqueController::class);
});

// Public/Authenticated routes for documents (download, embed, info & library)
Route::middleware('auth')->group(function () {
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download')->middleware('throttle:30,1');
    Route::get('/documents/{document}/embed', [DocumentController::class, 'embed'])->name('documents.embed')->middleware('throttle:60,1');
    Route::get('/documents/{document}/info', [DocumentController::class, 'info'])->name('documents.info');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/calendar', [EventController::class, 'calendar'])->name('events.calendar');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
});

// Espace Élus routes (requires authenticated user with élu or admin role)
Route::prefix('elus')->name('elus.')->middleware(['auth', 'elu'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/onboarding-complete', [DashboardController::class, 'onboardingComplete'])->name('onboarding.complete');
    Route::get('/weather-by-coords', [DashboardController::class, 'weatherByCoords'])->name('weather.by-coords');

    // Instances (Comités, Bureaux, Commissions)
    Route::resource('instances', InstanceController::class)->only(['index', 'show']);

    // Projects — read-only routes
    Route::resource('projects', ElusProjectController::class)->only(['index', 'show']);
    // Projects — mutation routes require admin
    Route::middleware('can:admin')->group(function () {
        Route::resource('projects', ElusProjectController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    });
    Route::get('/projects/geojson', [ElusProjectController::class, 'geojson'])->name('projects.geojson');

    // Reunions — specific routes before resource wildcards
    Route::get('/reunions/calendar', [ElusReunionController::class, 'calendar'])->name('reunions.calendar');
    Route::get('/reunions/json', [ElusReunionController::class, 'json'])->name('reunions.json');
    Route::post('/reunions/toggle-calendar', [ElusReunionController::class, 'toggleCalendar'])->name('reunions.toggle-calendar');
    // Reunions — read-only routes
    Route::resource('reunions', ElusReunionController::class)->only(['index', 'show']);
    // Reunions — mutation routes require admin
    Route::middleware('can:admin')->group(function () {
        Route::resource('reunions', ElusReunionController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    });

    // Documents
    Route::get('/documents', [ElusDocumentController::class, 'index'])->name('documents.index');
    Route::middleware('can:admin')->group(function () {
        Route::get('/documents/create', [ElusDocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [ElusDocumentController::class, 'store'])->name('documents.store');
    });

    // Forum (remplace l'ancien module Collaboratif)
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/creer', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{forum_thread}', [ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/{forum_thread}/posts', [ForumController::class, 'storePost'])->name('forum.posts.store');
    Route::put('/forum/{forum_thread}/posts/{forumPost}/detach-reply', [ForumController::class, 'detachReply'])->name('forum.posts.detach-reply');
    Route::put('/forum/{forum_thread}/posts/{forumPost}', [ForumController::class, 'update'])->name('forum.posts.update');
    Route::delete('/forum/{forum_thread}/posts/{forumPost}', [ForumController::class, 'destroy'])->name('forum.posts.destroy');

    // Actualités
    Route::get('/actualites', [ElusActualiteController::class, 'index'])->name('actualites.index');
    Route::get('/actualites/{actualite}', [ElusActualiteController::class, 'show'])->name('actualites.show');

    // Admin section (only for admins within Espace Élus)
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeElu'])->name('users.store');
        Route::get('/users/import', [AdminController::class, 'importForm'])->name('users.import.form');
        Route::post('/users/import', [AdminController::class, 'importCsv'])->name('users.import');
        Route::patch('/users/{user}/toggle-elu', [AdminController::class, 'toggleElu'])->name('users.toggle-elu');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
