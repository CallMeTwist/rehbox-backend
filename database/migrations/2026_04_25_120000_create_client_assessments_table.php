<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained()->cascadeOnDelete();

            // Step 1 — medical
            $table->text('medical_conditions');
            $table->unsignedSmallInteger('height_cm');
            $table->unsignedSmallInteger('weight_kg');
            $table->text('past_injuries');
            $table->text('allergies');
            $table->text('current_medications')->nullable();
            $table->text('family_health_history')->nullable();

            // Step 2 — lifestyle
            $table->boolean('smokes');
            $table->enum('alcohol_consumption', ['rarely', 'occasionally', 'all_the_time']);
            $table->text('diet_preferences');
            $table->unsignedTinyInteger('stress_level');

            // Step 3 — goals
            $table->json('primary_goals');
            $table->text('secondary_goals');
            $table->enum('time_frame', ['30d', '60d', '90d', '6mo', '1yr']);

            // Step 4 — habits
            $table->enum('exercise_habit', ['newbie', 'warrior', 'none']);
            $table->string('weekly_schedule');
            $table->json('comfort_level');
            $table->text('limitations')->nullable();
            $table->enum('best_time', ['morning', 'afternoon', 'evening']);
            $table->enum('feedback_frequency', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->json('feedback_type');
            $table->enum('feedback_channel', ['email', 'in_person', 'whatsapp']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_assessments');
    }
};
