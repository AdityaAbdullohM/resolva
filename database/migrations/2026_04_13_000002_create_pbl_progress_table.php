<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pbl_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('validated_to')->default(0);
            $table->timestamps();
            $table->unique(['problem_id','user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pbl_progress');
    }
};
