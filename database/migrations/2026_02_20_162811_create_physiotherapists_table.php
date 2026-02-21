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
        Schema::create('physiotherapists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('license_number');
            $table->string('hospital_or_clinic')->nullable();
            $table->string('specialty')->nullable();
            $table->string('phone');
            $table->string('city')->nullable();
            $table->string('country')->default('Nigeria');
            $table->string('credential_document_path')->nullable(); // uploaded PDF/image
            $table->string('profile_photo_path')->nullable();
            $table->enum('vetting_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('vetted_at')->nullable();
            $table->string('activation_code')->unique()->nullable(); // for onboarding clients
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physiotherapists');
    }
};
