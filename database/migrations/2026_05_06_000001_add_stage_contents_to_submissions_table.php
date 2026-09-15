<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'stage_contents')) {
                $table->json('stage_contents')->nullable()->after('file_path');
            }
        });
    }

    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'stage_contents')) {
                $table->dropColumn('stage_contents');
            }
        });
    }
};
