<?php

use App\Models\Physiotherapist;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/credentials/{id}', function (int $id) {
    // Must be logged in as admin
    if (auth()->guest() || auth()->user()->role !== 'admin') {
        abort(403);
    }

    $pt   = Physiotherapist::findOrFail($id);
    $path = $pt->credential_document_path;

    if (!$path || !Storage::disk('private')->exists($path)) {
        abort(404, 'Document not found.');
    }

    return Storage::disk('private')->response($path);
})->name('credentials.view');
