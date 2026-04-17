<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->enum('exercise_type', ['fundamental_rom', 'composite', 'mobility'])
                ->default('composite')
                ->after('is_personalized');
            $table->json('tracking_config')->nullable()->after('exercise_type');
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn(['exercise_type', 'tracking_config']);
        });
    }
};
