<?php

use App\Jobs\ExtractVideoThumbnailJob;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('dispatches a thumbnail job when video_path is created', function () {
    Bus::fake();
    Storage::fake('public');

    $exercise = Exercise::factory()->create([
        'video_source' => 'upload',
        'video_path' => 'exercises/videos/back/strengthening/test.mp4',
    ]);

    Bus::assertDispatched(ExtractVideoThumbnailJob::class, fn ($job) => $job->exerciseId === $exercise->id);
});

it('does not dispatch for youtube videos', function () {
    Bus::fake();

    Exercise::factory()->free()->create();

    Bus::assertNotDispatched(ExtractVideoThumbnailJob::class);
});

it('does not dispatch when video_path is unchanged on update', function () {
    Bus::fake();
    Storage::fake('public');

    $exercise = Exercise::factory()->create([
        'video_source' => 'upload',
        'video_path' => 'exercises/videos/back/x.mp4',
    ]);

    Bus::assertDispatchedTimes(ExtractVideoThumbnailJob::class, 1);

    $exercise->update(['title' => 'New title']);

    Bus::assertDispatchedTimes(ExtractVideoThumbnailJob::class, 1);
});
