<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pins MODIFY status ENUM('pending','verified','resolved','rejected') NOT NULL DEFAULT 'pending'");
        DB::statement("UPDATE pins SET status = 'rejected' WHERE status = 'resolved'");
        DB::statement("ALTER TABLE pins MODIFY status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending'");
        
        // Check if column exists before adding
        if (!Schema::hasColumn('pins', 'rejection_comment')) {
            DB::statement("ALTER TABLE pins ADD COLUMN rejection_comment TEXT NULL AFTER status");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pins MODIFY status ENUM('pending','verified','resolved','rejected') NOT NULL DEFAULT 'pending'");
        DB::statement("UPDATE pins SET status = 'resolved' WHERE status = 'rejected'");
        DB::statement("ALTER TABLE pins MODIFY status ENUM('pending','verified','resolved') NOT NULL DEFAULT 'pending'");
        
        if (Schema::hasColumn('pins', 'rejection_comment')) {
            DB::statement("ALTER TABLE pins DROP COLUMN rejection_comment");
        }
    }
};
