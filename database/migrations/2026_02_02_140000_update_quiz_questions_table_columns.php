<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_questions', 'question')) {
                $table->renameColumn('question', 'pertanyaan');
            }
            if (Schema::hasColumn('quiz_questions', 'options')) {
                $table->renameColumn('options', 'opsi_jawaban');
            }
            if (Schema::hasColumn('quiz_questions', 'correct_answer')) {
                $table->renameColumn('correct_answer', 'jawaban_benar');
            }
            if (!Schema::hasColumn('quiz_questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('jawaban_benar');
            }
            if (Schema::hasColumn('quiz_questions', 'points')) {
                $table->dropColumn('points');
            }
        });

        // Update existing data to match new enum values before altering column
        if (Schema::hasColumn('quiz_questions', 'question_type')) {
            // Temporarily change column type to string to accommodate longer values
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->string('question_type', 20)->nullable()->change(); // Make it nullable temporarily as well.
            });

            DB::table('quiz_questions')
                ->where('question_type', 'multiple_choice')
                ->update(['question_type' => 'pilihan_ganda']);
            DB::table('quiz_questions')
                ->where('question_type', 'essay')
                ->update(['question_type' => 'isian']);

            DB::statement("ALTER TABLE quiz_questions CHANGE question_type tipe ENUM('pilihan_ganda', 'benar_salah', 'isian') NOT NULL DEFAULT 'pilihan_ganda'");
        } else {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->enum('tipe', ['pilihan_ganda', 'benar_salah', 'isian'])->default('pilihan_ganda')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_questions', 'pertanyaan')) {
                $table->renameColumn('pertanyaan', 'question');
            }
            if (Schema::hasColumn('quiz_questions', 'opsi_jawaban')) {
                $table->renameColumn('opsi_jawaban', 'options');
            }
            if (Schema::hasColumn('quiz_questions', 'jawaban_benar')) {
                $table->renameColumn('jawaban_benar', 'correct_answer');
            }
            if (Schema::hasColumn('quiz_questions', 'explanation')) {
                $table->dropColumn('explanation');
            }
            if (!Schema::hasColumn('quiz_questions', 'points')) {
                $table->unsignedInteger('points')->default(10);
            }
        });
        
        if (Schema::hasColumn('quiz_questions', 'tipe')) {
            // Update existing data to match old enum values before altering column
            DB::table('quiz_questions')
                ->where('tipe', 'pilihan_ganda')
                ->update(['tipe' => 'multiple_choice']);
            DB::table('quiz_questions')
                ->where('tipe', 'isian')
                ->update(['tipe' => 'essay']);
            // Map 'benar_salah' to 'multiple_choice' or handle as appropriate if it exists
            DB::table('quiz_questions')
                ->where('tipe', 'benar_salah')
                ->update(['tipe' => 'multiple_choice']); // Defaulting to multiple_choice


            DB::statement("ALTER TABLE quiz_questions CHANGE tipe question_type ENUM('multiple_choice', 'essay') NOT NULL DEFAULT 'multiple_choice'");
        } else {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->enum('question_type', ['multiple_choice', 'essay'])->default('multiple_choice')->change();
            });
        }
    }
};