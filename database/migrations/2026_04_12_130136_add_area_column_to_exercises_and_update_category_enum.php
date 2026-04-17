<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Ensure exercises table is empty before changing ENUMs
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE plan_exercises');
        DB::statement('TRUNCATE TABLE exercise_sessions');
        DB::statement('TRUNCATE TABLE exercise_plans');
        DB::statement('TRUNCATE TABLE exercises');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Add area column if it doesn't already exist
        $columns = DB::select("SHOW COLUMNS FROM exercises LIKE 'area'");
        if (empty($columns)) {
            DB::statement("ALTER TABLE exercises ADD COLUMN area ENUM('neck','shoulder','elbow_forearm_wrist','back','lower_limb') NOT NULL DEFAULT 'neck' AFTER title");
        } else {
            DB::statement("ALTER TABLE exercises MODIFY COLUMN area ENUM('neck','shoulder','elbow_forearm_wrist','back','lower_limb') NOT NULL DEFAULT 'neck'");
        }

        // Re-type category column to exercise type
        DB::statement("ALTER TABLE exercises MODIFY COLUMN category ENUM('strengthening','stretching','rom','functional','endurance') NOT NULL DEFAULT 'strengthening'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE exercises MODIFY COLUMN category ENUM('neck','shoulder','elbow_forearm_wrist','back','lower_limb') NOT NULL DEFAULT 'neck'");
        DB::statement('ALTER TABLE exercises DROP COLUMN area');
    }
};
