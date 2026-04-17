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

        DB::statement("ALTER TABLE exercises MODIFY COLUMN category ENUM('neck', 'shoulder', 'elbow_forearm_wrist', 'back', 'lower_limb') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE exercises MODIFY COLUMN category ENUM('head_neck', 'upper_limb', 'back', 'lower_limb') NOT NULL");
    }
};
