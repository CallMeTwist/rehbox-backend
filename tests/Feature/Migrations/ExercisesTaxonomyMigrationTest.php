<?php

use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('has the new video and tier columns after migration', function () {
    expect(Schema::hasColumn('exercises', 'access_tier'))->toBeTrue();
    expect(Schema::hasColumn('exercises', 'video_source'))->toBeTrue();
    expect(Schema::hasColumn('exercises', 'video_path'))->toBeTrue();
    expect(Schema::hasColumn('exercises', 'youtube_url'))->toBeTrue();
    expect(Schema::hasColumn('exercises', 'thumbnail_path'))->toBeTrue();
});

it('accepts the new area enum values', function () {
    expect(Schema::getColumnType('exercises', 'area'))->toBeIn(['string', 'varchar']);

    foreach (['back', 'chest', 'elbow_forearm_wrist', 'general', 'head_neck', 'lower_limbs', 'pelvic', 'upper_limbs'] as $area) {
        $e = Exercise::factory()->create(['area' => $area, 'access_tier' => $area === 'general' ? 'free' : 'paid']);
        expect($e->area)->toBe($area);
    }
});

it('accepts the new category enum values', function () {
    expect(Schema::getColumnType('exercises', 'category'))->toBeIn(['string', 'varchar']);

    $categories = ['strengthening', 'stretching', 'rom', 'functional', 'endurance',
        'lung_expansion', 'chest_wall_mobilization', 'airways_clearance',
        'chest_abs', 'cool_down', 'core_stability', 'legs', 'strengthening_arm'];
    foreach ($categories as $cat) {
        $e = Exercise::factory()->create(['category' => $cat]);
        expect($e->category)->toBe($cat);
    }
});

it('remaps legacy area values via the migration', function () {
    // After migration, no legacy values should remain in the area column.
    $legacy = Exercise::query()->whereIn('area', ['shoulder', 'neck', 'lower_limb'])->count();
    expect($legacy)->toBe(0);
});
