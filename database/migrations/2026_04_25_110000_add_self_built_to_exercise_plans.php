<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_plans', function (Blueprint $table) {
            $table->foreignId('physiotherapist_id')->nullable()->change();
            $table->foreignId('created_by_client_id')
                ->nullable()
                ->after('client_id')
                ->constrained('clients')
                ->nullOnDelete();
            $table->boolean('is_self_built')->default(false)->after('created_by_client_id');
        });

        Schema::table('plan_exercises', function (Blueprint $table) {
            $table->json('scheduled_days')->nullable()->after('pt_notes');
        });
    }

    public function down(): void
    {
        Schema::table('plan_exercises', function (Blueprint $table) {
            $table->dropColumn('scheduled_days');
        });

        Schema::table('exercise_plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_client_id');
            $table->dropColumn('is_self_built');
            $table->foreignId('physiotherapist_id')->nullable(false)->change();
        });
    }
};
