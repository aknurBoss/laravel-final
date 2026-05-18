<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('started_at');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('students_count')->default(0);
            $table->unsignedInteger('records_count')->default(0);
            $table->integer('total_merits')->default(0);
            $table->integer('total_demerits')->default(0);
            $table->timestamps();

            $table->index('closed_at');
        });

        Schema::create('semester_student_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->string('student_name');
            $table->string('class_name')->nullable();
            $table->integer('final_points');
            $table->unsignedInteger('global_rank');
            $table->unsignedInteger('class_rank')->nullable();
            $table->timestamps();

            $table->unique(['semester_id', 'student_id']);
            $table->index(['semester_id', 'final_points']);
        });

        Schema::table('point_histories', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        $startingPoints = (int) config('discipline.starting_points', 100);
        $now = now();

        $semesterId = DB::table('semesters')->insertGetId([
            'name'           => 'Текущий семестр',
            'started_at'     => $now,
            'closed_at'      => null,
            'students_count' => 0,
            'records_count'  => 0,
            'total_merits'   => 0,
            'total_demerits' => 0,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        DB::table('point_histories')->whereNull('semester_id')->update(['semester_id' => $semesterId]);
    }

    public function down(): void
    {
        Schema::table('point_histories', function (Blueprint $table) {
            if (Schema::hasColumn('point_histories', 'semester_id')) {
                $table->dropForeign(['semester_id']);
                $table->dropColumn('semester_id');
            }
        });

        Schema::dropIfExists('semester_student_snapshots');
        Schema::dropIfExists('semesters');
    }
};
