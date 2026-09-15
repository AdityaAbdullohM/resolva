<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            if (!Schema::hasColumn('discussions', 'group_id')) {
                $table->foreignId('group_id')->nullable()->after('kelas_id')->constrained('groups')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            if (Schema::hasColumn('discussions', 'group_id')) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            }
        });
    }
};
