<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        // 1. Ensure area column exists.
        //    On MySQL it was added in a prior migration; on SQLite that migration is a no-op,
        //    so we may need to create it here.
        if (! Schema::hasColumn('exercises', 'area')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->string('area', 50)->nullable()->after('title');
            });
        }

        if ($isMysql) {
            // 2a. MySQL: relax area + category from ENUM to varchar(50) via ALTER COLUMN.
            //     Must happen before the UPDATE so MySQL doesn't reject the new values.
            Schema::table('exercises', function (Blueprint $table) {
                $table->string('area', 50)->change();
                $table->string('category', 50)->change();
            });
        } else {
            // 2b. SQLite: cannot ALTER a column to remove a CHECK constraint.
            //     Rename, recreate, copy, drop — the standard SQLite column-type migration pattern.
            Schema::table('exercises', function (Blueprint $table) {
                $table->renameColumn('category', 'category_old');
            });
            Schema::table('exercises', function (Blueprint $table) {
                $table->string('category', 50)->default('strengthening')->after('area');
            });
            DB::statement('UPDATE exercises SET category = category_old');
            Schema::table('exercises', function (Blueprint $table) {
                $table->dropColumn('category_old');
            });
        }

        // 3. Remap legacy area values.
        DB::table('exercises')->where('area', 'shoulder')->update(['area' => 'upper_limbs']);
        DB::table('exercises')->where('area', 'neck')->update(['area' => 'head_neck']);
        DB::table('exercises')->where('area', 'lower_limb')->update(['area' => 'lower_limbs']);

        // 4. Add new columns. enum() emits ENUM on MySQL and a CHECK constraint on SQLite — both work.
        Schema::table('exercises', function (Blueprint $table) {
            $table->enum('access_tier', ['free', 'paid'])->default('paid')->after('difficulty');
            $table->enum('video_source', ['youtube', 'upload'])->nullable()->after('access_tier');
            $table->string('video_path', 500)->nullable()->after('video_url');
            $table->string('youtube_url', 500)->nullable()->after('video_path');
            $table->string('thumbnail_path', 500)->nullable()->after('illustration_url');
        });

        // 5. Backfill video_source / youtube_url / video_path from legacy video_url.
        DB::table('exercises')
            ->whereNotNull('video_url')
            ->where(function ($q) {
                $q->where('video_url', 'like', '%youtube.com%')
                    ->orWhere('video_url', 'like', '%youtu.be%');
            })
            ->update([
                'video_source' => 'youtube',
                'youtube_url' => DB::raw('video_url'),
            ]);

        DB::table('exercises')
            ->whereNotNull('video_url')
            ->whereNull('video_source')
            ->update([
                'video_source' => 'upload',
                'video_path' => DB::raw('video_url'),
            ]);
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn(['access_tier', 'video_source', 'video_path', 'youtube_url', 'thumbnail_path']);
        });

        $isMysql = DB::getDriverName() === 'mysql';

        if ($isMysql) {
            // Restore area and category back to their pre-Task-1 ENUM definitions.
            Schema::table('exercises', function (Blueprint $table) {
                $table->enum('area', ['neck', 'shoulder', 'elbow_forearm_wrist', 'back', 'lower_limb'])
                    ->default('neck')
                    ->change();
                $table->enum('category', ['strengthening', 'stretching', 'rom', 'functional', 'endurance'])
                    ->default('strengthening')
                    ->change();
            });
        }
        // SQLite: varchar(50) columns are already correct; re-running up() on a fresh DB rebuilds fine.
    }
};
