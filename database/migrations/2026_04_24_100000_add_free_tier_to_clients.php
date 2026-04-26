<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Renames the 'basic' tier to 'free' in clients.subscription_plan.
 *
 * WARNING: down() is lossy in production — any rows that were genuinely
 * created as 'free' (never 'basic') will be coerced to 'basic' on rollback.
 * Intended for dev/CI rollback only. Run migrations in maintenance mode
 * (`php artisan down`) in any environment with live writes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('subscription_plan_new', ['free', 'standard', 'enterprise'])
                ->default('free')
                ->after('subscription_plan');
        });

        $unmapped = DB::table('clients')
            ->where(function ($q) {
                $q->whereNull('subscription_plan')
                    ->orWhereNotIn('subscription_plan', ['basic', 'standard', 'enterprise']);
            })
            ->count();

        if ($unmapped > 0) {
            throw new \RuntimeException("Refusing to migrate: {$unmapped} clients have unmapped subscription_plan values.");
        }

        DB::statement("UPDATE clients SET subscription_plan_new = CASE subscription_plan
            WHEN 'basic' THEN 'free'
            WHEN 'standard' THEN 'standard'
            WHEN 'enterprise' THEN 'enterprise'
            END");

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('subscription_plan');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('subscription_plan_new', 'subscription_plan');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('subscription_plan_old', ['basic', 'standard', 'enterprise'])
                ->default('basic')
                ->after('subscription_plan');
        });

        $unmapped = DB::table('clients')
            ->where(function ($q) {
                $q->whereNull('subscription_plan')
                    ->orWhereNotIn('subscription_plan', ['free', 'standard', 'enterprise']);
            })
            ->count();

        if ($unmapped > 0) {
            throw new \RuntimeException("Refusing to migrate: {$unmapped} clients have unmapped subscription_plan values.");
        }

        DB::statement("UPDATE clients SET subscription_plan_old = CASE subscription_plan
            WHEN 'free' THEN 'basic'
            WHEN 'standard' THEN 'standard'
            WHEN 'enterprise' THEN 'enterprise'
            END");

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('subscription_plan');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('subscription_plan_old', 'subscription_plan');
        });
    }
};
