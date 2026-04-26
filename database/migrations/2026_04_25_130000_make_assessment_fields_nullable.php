<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_assessments', function (Blueprint $table) {
            $table->text('medical_conditions')->nullable()->change();
            $table->text('past_injuries')->nullable()->change();
            $table->text('allergies')->nullable()->change();
            $table->text('diet_preferences')->nullable()->change();
            $table->text('secondary_goals')->nullable()->change();
            $table->string('weekly_schedule')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('client_assessments', function (Blueprint $table) {
            $table->text('medical_conditions')->nullable(false)->change();
            $table->text('past_injuries')->nullable(false)->change();
            $table->text('allergies')->nullable(false)->change();
            $table->text('diet_preferences')->nullable(false)->change();
            $table->text('secondary_goals')->nullable(false)->change();
            $table->string('weekly_schedule')->nullable(false)->change();
        });
    }
};
