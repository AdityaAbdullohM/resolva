<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Try to alter groups.problem_id to be nullable and set FK to ON DELETE SET NULL
        try {
            DB::statement('ALTER TABLE `groups` DROP FOREIGN KEY `groups_problem_id_foreign`');
        } catch (\Exception $e) {
            // ignore if constraint does not exist
        }

        // Make column nullable
        try {
            DB::statement('ALTER TABLE `groups` MODIFY `problem_id` bigint unsigned NULL');
        } catch (\Exception $e) {
            // ignore
        }

        // Recreate foreign key with ON DELETE SET NULL
        try {
            DB::statement('ALTER TABLE `groups` ADD CONSTRAINT `groups_problem_id_foreign` FOREIGN KEY (`problem_id`) REFERENCES `problems` (`id`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // ignore
        }
    }

    public function down()
    {
        try {
            DB::statement('ALTER TABLE `groups` DROP FOREIGN KEY `groups_problem_id_foreign`');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE `groups` MODIFY `problem_id` bigint unsigned NOT NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE `groups` ADD CONSTRAINT `groups_problem_id_foreign` FOREIGN KEY (`problem_id`) REFERENCES `problems` (`id`) ON DELETE CASCADE');
        } catch (\Exception $e) {}
    }
};
