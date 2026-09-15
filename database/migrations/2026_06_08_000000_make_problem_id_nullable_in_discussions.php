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
        Schema::table('discussions', function (Blueprint $table) {
            // drop existing foreign and column, then recreate nullable
            if (Schema::hasColumn('discussions', 'problem_id')) {
                $table->dropForeign(['problem_id']);
                $table->dropColumn('problem_id');
            }
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->foreignId('problem_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            if (Schema::hasColumn('discussions', 'problem_id')) {
                $table->dropForeign(['problem_id']);
                $table->dropColumn('problem_id');
            }
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->foreignId('problem_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
