<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'subject')) {
                $table->dropColumn('subject');
            }
            if (! Schema::hasColumn('teachers', 'is_homeroom_teacher')) {
                $table->boolean('is_homeroom_teacher')->default(false)->after('user_id');
            }
        });

        Schema::table('school_classes', function (Blueprint $table) {
            if (! Schema::hasColumn('school_classes', 'homeroom_teacher_id')) {
                $table->foreignId('homeroom_teacher_id')
                    ->nullable()
                    ->unique()
                    ->after('name')
                    ->constrained('teachers')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            if (Schema::hasColumn('school_classes', 'homeroom_teacher_id')) {
                $table->dropForeign(['homeroom_teacher_id']);
                $table->dropColumn('homeroom_teacher_id');
            }
        });

        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'is_homeroom_teacher')) {
                $table->dropColumn('is_homeroom_teacher');
            }
            if (! Schema::hasColumn('teachers', 'subject')) {
                $table->string('subject')->nullable();
            }
        });
    }
};
