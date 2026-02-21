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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('physiotherapist_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('primary_condition')->nullable(); // e.g. "chronic back pain"
            $table->enum('subscription_status', ['inactive', 'active', 'expired'])->default('inactive');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->string('paystack_customer_code')->nullable();
            $table->enum('language_preference', ['en', 'pcm', 'yo', 'ig', 'ha'])->default('en');
            $table->integer('coin_balance')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
