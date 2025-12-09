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
        Schema::table('subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('subjects', 'schedule')) {
                $table->string('schedule')->nullable()->after('hours_per_week');
            }
            if (! Schema::hasColumn('subjects', 'time')) {
                $table->string('time')->nullable()->after('schedule');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'schedule')) {
                $table->dropColumn('schedule');
            }
            if (Schema::hasColumn('subjects', 'time')) {
                $table->dropColumn('time');
            }
        });
    }
};
