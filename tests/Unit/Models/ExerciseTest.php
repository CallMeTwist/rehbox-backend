<?php

use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('extracts youtube id from standard watch URL', function () {
    $e = new Exercise(['video_source' => 'youtube', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);
    expect($e->youtubeId())->toBe('dQw4w9WgXcQ');
});

it('extracts youtube id from short youtu.be URL', function () {
    $e = new Exercise(['video_source' => 'youtube', 'youtube_url' => 'https://youtu.be/dQw4w9WgXcQ?t=12']);
    expect($e->youtubeId())->toBe('dQw4w9WgXcQ');
});

it('returns null when video_source is not youtube', function () {
    $e = new Exercise(['video_source' => 'upload', 'video_path' => 'foo.mp4']);
    expect($e->youtubeId())->toBeNull();
});

it('returns null when youtube_url is missing', function () {
    $e = new Exercise(['video_source' => 'youtube', 'youtube_url' => null]);
    expect($e->youtubeId())->toBeNull();
});

it('casts new fields correctly', function () {
    $e = Exercise::factory()->create(['access_tier' => 'free', 'video_source' => 'youtube']);
    expect($e->access_tier)->toBe('free');
    expect($e->video_source)->toBe('youtube');
});
