<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $starting = (int) config('discipline.starting_points', 100);

        if (! Schema::hasColumn('students', 'current_points')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY current_points INT NOT NULL DEFAULT {$starting}");
        } elseif ($driver === 'sqlite') {
            // SQLite не поддерживает MODIFY — только обновляем данные
        }

        DB::table('students')->where('current_points', 10)->update(['current_points' => $starting]);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('students', 'current_points')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE students MODIFY current_points INT NOT NULL DEFAULT 10');
        }
    }
};
