<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pins MODIFY type ENUM('incident','dumping','flood','water','hotspot','biohazard') NOT NULL DEFAULT 'incident'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pins MODIFY type ENUM('incident','dumping','flood','water','hotspot') NOT NULL DEFAULT 'incident'");
    }
};
