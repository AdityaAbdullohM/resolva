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
        // Remove ON UPDATE CURRENT_TIMESTAMP which causes start_time to change on row updates
        DB::statement("ALTER TABLE `quiz_attempts` MODIFY `start_time` TIMESTAMP NULL DEFAULT NULL");
        DB::statement("ALTER TABLE `quiz_attempts` MODIFY `end_time` TIMESTAMP NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore previous behavior for start_time (if needed).
        DB::statement("ALTER TABLE `quiz_attempts` MODIFY `start_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        DB::statement("ALTER TABLE `quiz_attempts` MODIFY `end_time` TIMESTAMP NULL DEFAULT NULL");
    }
};
