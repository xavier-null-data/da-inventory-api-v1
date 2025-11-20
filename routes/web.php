<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Serve static Swagger documentation
// Serve Swagger UI files
Route::get('/docs/{path?}', function ($path = null) {
    $fullPath = public_path('docs/' . ($path ?? 'index.html'));

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mime = mime_content_type($fullPath);
    return response()->file($fullPath, ['Content-Type' => $mime]);
})->where('path', '.*');



Route::get('/docs', function () {
    return redirect('/docs/index.html');
});
