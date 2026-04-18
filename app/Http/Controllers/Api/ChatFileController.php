<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatFileController extends Controller
{
    public function show(Request $request, string $filename): StreamedResponse
    {
        $path = 'chat-files/'.basename($filename);

        abort_unless(Storage::disk('public')->exists($path), 404);

        $mime = Storage::disk('public')->mimeType($path);
        $size = Storage::disk('public')->size($path);

        return response()->streamDownload(
            fn () => fpassthru(Storage::disk('public')->readStream($path)),
            $filename,
            [
                'Content-Type' => $mime,
                'Content-Length' => $size,
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]
        );
    }
}
