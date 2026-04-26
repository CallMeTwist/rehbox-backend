<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('clients')
            ->whereNull('subscription_plan')
            ->update(['subscription_plan' => 'free']);
    }

    public function down(): void
    {
        // no-op
    }
};
