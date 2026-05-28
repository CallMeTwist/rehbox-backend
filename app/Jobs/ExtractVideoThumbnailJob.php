<?php

namespace App\Jobs;

use App\Models\Exercise;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ExtractVideoThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $exerciseId) {}

    public function handle(): void
    {
        $exercise = Exercise::find($this->exerciseId);
        if (! $exercise || ! $exercise->video_path) {
            return;
        }

        $disk = Storage::disk(config('rehbox.exercise_video_disk'));
        if (! $disk->exists($exercise->video_path)) {
            Log::warning("ExtractVideoThumbnailJob: source video missing for exercise {$exercise->id}");

            return;
        }

        $sourcePath = $disk->path($exercise->video_path);
        $thumbName = pathinfo($exercise->video_path, PATHINFO_FILENAME).'.jpg';
        $thumbPath = 'exercises/thumbnails/'.$thumbName;
        $thumbAbs = $disk->path($thumbPath);

        @mkdir(dirname($thumbAbs), 0775, true);

        $process = new Process([
            'ffmpeg', '-y', '-ss', '00:00:01', '-i', $sourcePath,
            '-frames:v', '1', '-q:v', '2', $thumbAbs,
        ]);
        $process->setTimeout(60);

        try {
            $process->mustRun();
        } catch (\Throwable $e) {
            Log::warning("ExtractVideoThumbnailJob failed for exercise {$exercise->id}: ".$e->getMessage());

            return;
        }

        $exercise->update(['thumbnail_path' => $thumbPath]);
    }
}
