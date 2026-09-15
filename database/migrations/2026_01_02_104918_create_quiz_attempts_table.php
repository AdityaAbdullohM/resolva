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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Student
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->enum('status', ['started', 'finished', 'graded'])->default('started');
            $table->timestamps();

            $table->unique(['quiz_id', 'user_id']); // A user can only attempt a quiz once
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
