<?php

namespace App\Observers;

use App\Jobs\ExtractVideoThumbnailJob;
use App\Models\Exercise;

class ExerciseObserver
{
    public function created(Exercise $exercise): void
    {
        $this->maybeDispatchThumbnail($exercise);
    }

    public function updated(Exercise $exercise): void
    {
        if ($exercise->wasChanged('video_path')) {
            $this->maybeDispatchThumbnail($exercise);
        }
    }

    private function maybeDispatchThumbnail(Exercise $exercise): void
    {
        if ($exercise->video_source === 'upload' && $exercise->video_path) {
            ExtractVideoThumbnailJob::dispatch($exercise->id);
        }
    }
}
