<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            if (! Schema::hasColumn('tickets', 'due_at')) {
                $table->timestamp('due_at')->nullable()->after('attachment_path');
            }
            if (! Schema::hasColumn('tickets', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('due_at');
            }
            if (! Schema::hasColumn('tickets', 'rating_comment')) {
                $table->text('rating_comment')->nullable()->after('rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropColumn(['due_at', 'rating', 'rating_comment']);
        });
    }
};
