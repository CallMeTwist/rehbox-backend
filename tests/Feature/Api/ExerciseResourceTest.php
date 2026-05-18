<?php

use App\Http\Resources\ExerciseResource;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

function transformExercise(Exercise $e, ?User $user): array
{
    $req = Request::create('/');
    if ($user) {
        $req->setUserResolver(fn () => $user);
    }

    return (new ExerciseResource($e))->toArray($req);
}

it('locks a paid exercise for a free client', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    $exercise = Exercise::factory()->create([
        'access_tier' => 'paid', 'video_source' => 'upload', 'video_path' => 'exercises/videos/back/x.mp4',
        'correct_angles' => ['hip' => 90],
        'illustration_url' => 'https://example.com/foo.png',
    ]);

    $out = transformExercise($exercise->refresh(), $user->refresh());
    expect($out['is_locked'])->toBeTrue();
    expect($out['video']['url'])->toBeNull();
    expect($out['video']['youtube_id'])->toBeNull();
    expect($out)->not->toHaveKey('correct_angles');
    expect($out['thumbnail_url'])->not->toBeNull();
});

it('does not lock a free exercise for a free client', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    $exercise = Exercise::factory()->free()->create();

    $out = transformExercise($exercise->refresh(), $user->refresh());
    expect($out['is_locked'])->toBeFalse();
    expect($out['video']['source'])->toBe('youtube');
    expect($out['video']['youtube_id'])->toBe('dQw4w9WgXcQ');
});

it('never locks paid exercises for PT users', function () {
    $user = User::factory()->create(['role' => 'pt']);

    $exercise = Exercise::factory()->create([
        'access_tier' => 'paid', 'video_source' => 'upload', 'video_path' => 'exercises/videos/back/x.mp4',
    ]);

    $out = transformExercise($exercise->refresh(), $user->refresh());
    expect($out['is_locked'])->toBeFalse();
    expect($out['video']['url'])->not->toBeNull();
});

it('never locks paid exercises for paid clients', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);

    $exercise = Exercise::factory()->create([
        'access_tier' => 'paid', 'video_source' => 'upload', 'video_path' => 'exercises/videos/back/x.mp4',
    ]);

    $out = transformExercise($exercise->refresh(), $user->refresh());
    expect($out['is_locked'])->toBeFalse();
});

it('derives youtube thumbnail when illustration_url and thumbnail_path are null', function () {
    $exercise = Exercise::factory()->free()->create();
    $out = transformExercise($exercise->refresh(), null);
    expect($out['thumbnail_url'])->toContain('i.ytimg.com');
    expect($out['thumbnail_url'])->toContain('dQw4w9WgXcQ');
});
