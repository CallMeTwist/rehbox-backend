<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['head_neck', 'upper_limb', 'back', 'lower_limb']);
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('illustration_url')->nullable();
            $table->integer('default_sets')->default(3);
            $table->integer('default_reps')->default(10);
            $table->integer('default_hold_seconds')->default(0);
            // Multilingual instructions
            $table->text('instructions_en')->nullable();
            $table->text('instructions_pcm')->nullable(); // Pidgin
            $table->text('instructions_yo')->nullable();  // Yoruba
            $table->text('instructions_ig')->nullable();  // Igbo
            $table->text('instructions_ha')->nullable();  // Hausa
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
