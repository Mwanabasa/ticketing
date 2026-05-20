<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->timestamp('sla_breached_at')->nullable()->after('due_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropColumn('sla_breached_at');
        });
    }
};
