<?php

namespace App\Console\Commands;

use App\Models\Exercise;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImportExercisesFromFolder extends Command
{
    protected $signature = 'exercises:import-from-folder
                            {path : Absolute path to the root exercise videos folder}
                            {--dry-run : Print what would happen without making any changes}
                            {--force : Re-import exercises that already have a video_path set}';

    protected $description = 'Bulk-import exercise video files from a folder tree into the exercise library';

    private const AREA_MAP = [
        'Back' => ['area' => 'back',                'access_tier' => 'paid'],
        'Chest' => ['area' => 'chest',               'access_tier' => 'paid'],
        'Elbow, Fore-arm & Wrist' => ['area' => 'elbow_forearm_wrist', 'access_tier' => 'paid'],
        'General Exercises' => ['area' => 'general',             'access_tier' => 'free'],
        'Head & Neck' => ['area' => 'head_neck',           'access_tier' => 'paid'],
        'Lower Limbs' => ['area' => 'lower_limbs',         'access_tier' => 'paid'],
        'Pelvic' => ['area' => 'pelvic',              'access_tier' => 'paid'],
        'Upper Limbs' => ['area' => 'upper_limbs',         'access_tier' => 'paid'],
    ];

    private const CATEGORY_MAP = [
        'Strengthening' => 'strengthening',
        'Strengthening (Arm)' => 'strengthening_arm',
        'Stretching' => 'stretching',
        'Stretches' => 'stretching',
        'ROM Exercise' => 'rom',
        'ROM Exercises' => 'rom',
        'Functional Exercises' => 'functional',
        'Endurance' => 'endurance',
        'Chest wall Mobilization' => 'chest_wall_mobilization',
        'Lung Expansion Exercise' => 'lung_expansion',
        'Airways Clearance' => 'airways_clearance',
        'Chest & Abs' => 'chest_abs',
        'Cool Down (3-5 mins rest)' => 'cool_down',
        'Core stability' => 'core_stability',
        'Legs' => 'legs',
    ];

    public function handle(): int
    {
        $rootPath = rtrim($this->argument('path'), '/\\');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (! is_dir($rootPath)) {
            $this->error("Directory not found: {$rootPath}");

            return 1;
        }

        if ($dryRun) {
            $this->warn('DRY RUN — no files will be copied and no records will be written.');
        }

        $rows = [];
        $updated = 0;
        $created = 0;
        $skipped = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'mp4') {
                continue;
            }

            $subPath = $iterator->getSubPathname();
            $parts = preg_split('/[\/\\\\]/', $subPath);

            if (count($parts) !== 3) {
                $rows[] = [basename($subPath), '—', '—', 'SKIP — unexpected depth', ''];
                $skipped++;

                continue;
            }

            [$areaFolder, $categoryFolder, $filename] = $parts;

            if (! isset(self::AREA_MAP[$areaFolder])) {
                $rows[] = [$filename, '?', '—', 'SKIP — unknown area', ''];
                $skipped++;

                continue;
            }

            if (! isset(self::CATEGORY_MAP[$categoryFolder])) {
                $rows[] = [$filename, self::AREA_MAP[$areaFolder]['area'], '?', 'SKIP — unknown category', ''];
                $skipped++;

                continue;
            }

            $area = self::AREA_MAP[$areaFolder]['area'];
            $accessTier = self::AREA_MAP[$areaFolder]['access_tier'];
            $category = self::CATEGORY_MAP[$categoryFolder];
            $title = Str::headline(pathinfo($filename, PATHINFO_FILENAME));

            $exercise = Exercise::whereRaw('LOWER(title) = ?', [mb_strtolower($title)])
                ->where('area', $area)
                ->first()
                ?? Exercise::whereRaw('LOWER(title) = ?', [mb_strtolower($title)])->first();

            if ($exercise && $exercise->video_path && ! $force) {
                $rows[] = [$filename, $area, $category, 'SKIP — already has video', $exercise->title];
                $skipped++;

                continue;
            }

            $action = $exercise ? 'UPDATE (match)' : 'CREATE (new)';

            if (! $dryRun) {
                $slug = Str::slug($title);
                $uuid = Str::uuid();
                $storagePath = "exercises/videos/{$area}/{$category}/{$slug}-{$uuid}.mp4";

                Storage::disk('public')->putFileAs(
                    "exercises/videos/{$area}/{$category}",
                    new File($file->getPathname()),
                    "{$slug}-{$uuid}.mp4"
                );

                if ($exercise) {
                    $exercise->update([
                        'video_source' => 'upload',
                        'video_path' => $storagePath,
                        'access_tier' => $accessTier,
                    ]);
                    $updated++;
                } else {
                    $exercise = Exercise::create([
                        'title' => $title,
                        'area' => $area,
                        'category' => $category,
                        'difficulty' => 'beginner',
                        'default_sets' => 3,
                        'default_reps' => 10,
                        'video_source' => 'upload',
                        'video_path' => $storagePath,
                        'access_tier' => $accessTier,
                        'is_active' => true,
                    ]);
                    $created++;
                }
            }

            $rows[] = [$filename, $area, $category, $action, $exercise?->title ?? $title];
        }

        $this->table(['File', 'Area', 'Category', 'Action', 'Exercise title'], $rows);

        if ($dryRun) {
            $this->info('Dry run complete. Re-run without --dry-run to apply changes.');
        } else {
            $this->info("Done. Updated: {$updated} · Created: {$created} · Skipped: {$skipped}");
        }

        return 0;
    }
}
