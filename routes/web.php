<?php

use App\Models\Media;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/media/create', function(){
    return Inertia::render('CreateMedia');
});

Route::post('/media', function(){
    request()->validate([
        'file' => ['file', 'max:512000']
    ], [
        'max' => 'File cannot be larger than 512MB'
    ]);

    $file = request()->file('file');

    $media = Media::create([
        'name' => $file->getClientOriginalName(),
        'file_name' => $file->getClientOriginalName(),
        'mime_type' => $file->getMimeType(),
        'size' => $file->getSize(),
        'author_id' => auth()->id()
    ]);

    // media/year/month/day/id
    $directory = "media/{$media->created_at->format('Y/m/d')}/{$media->id}";

    $file->storeAs($directory, $media->file_name, 'public');
})->name('media.store');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
