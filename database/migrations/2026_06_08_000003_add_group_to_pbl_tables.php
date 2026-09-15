<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pbl_validations', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete()->after('user_id');
            $table->index(['problem_id','group_id']);
        });

        Schema::table('pbl_progress', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete()->after('user_id');
            $table->unique(['problem_id','group_id']);
        });
    }

    public function down()
    {
        Schema::table('pbl_validations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('group_id');
        });

        Schema::table('pbl_progress', function (Blueprint $table) {
            $table->dropConstrainedForeignId('group_id');
        });
    }
};
