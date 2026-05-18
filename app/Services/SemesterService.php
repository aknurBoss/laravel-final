<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PointHistory;
use App\Models\Semester;
use App\Models\SemesterStudentSnapshot;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SemesterService
{
    private ?int $cachedActiveId = null;

    public function active(): Semester
    {
        $semester = Semester::query()->active()->first();

        if ($semester) {
            $this->cachedActiveId = $semester->id;

            return $semester;
        }

        return $this->bootstrapActiveSemester();
    }

    public function activeSemesterId(): int
    {
        return $this->cachedActiveId ?? $this->active()->id;
    }

    public function closeAndStartNew(string $closedSemesterName, string $newSemesterName, User $admin): array
    {
        if (trim($closedSemesterName) === '' || trim($newSemesterName) === '') {
            throw ValidationException::withMessages([
                'name' => 'Укажите название семестра.',
            ]);
        }

        return DB::transaction(function () use ($closedSemesterName, $newSemesterName, $admin) {
            $active = Semester::query()->active()->lockForUpdate()->first();

            if (! $active) {
                throw ValidationException::withMessages([
                    'semester' => 'Нет активного семестра для закрытия.',
                ]);
            }

            $semesterId = $active->id;
            $startingPoints = Student::startingPoints();

            $histories = PointHistory::query()
                ->forSemester($semesterId)
                ->get();

            $totalMerits = (int) $histories->where('points', '>', 0)->sum('points');
            $totalDemerits = (int) abs($histories->where('points', '<', 0)->sum('points'));

            $students = Student::query()
                ->with(['user', 'schoolClass'])
                ->orderByDesc('current_points')
                ->orderBy('id')
                ->get();

            foreach ($students->values() as $index => $student) {
                $classRank = null;
                if ($student->class_id) {
                    $classRank = Student::query()
                        ->where('class_id', $student->class_id)
                        ->where('current_points', '>', $student->current_points)
                        ->count() + 1;
                }

                SemesterStudentSnapshot::create([
                    'semester_id'   => $semesterId,
                    'student_id'    => $student->id,
                    'class_id'      => $student->class_id,
                    'student_name'  => $student->user?->name ?? '—',
                    'class_name'    => $student->schoolClass?->name,
                    'final_points'  => (int) $student->current_points,
                    'global_rank'   => $index + 1,
                    'class_rank'    => $classRank,
                ]);
            }

            $active->update([
                'name'               => $closedSemesterName,
                'closed_at'          => now(),
                'closed_by_user_id'  => $admin->id,
                'students_count'     => $students->count(),
                'records_count'      => $histories->count(),
                'total_merits'       => $totalMerits,
                'total_demerits'     => $totalDemerits,
            ]);

            Student::query()->update(['current_points' => $startingPoints]);

            Notification::query()->delete();

            $newSemester = Semester::create([
                'name'       => $newSemesterName,
                'started_at' => now(),
            ]);

            $this->cachedActiveId = $newSemester->id;

            return [
                'closed' => $active->fresh(['closedBy']),
                'new'    => $newSemester,
                'starting_points' => $startingPoints,
            ];
        });
    }

    private function bootstrapActiveSemester(): Semester
    {
        $semester = Semester::create([
            'name'       => 'Текущий семестр',
            'started_at' => now(),
        ]);

        PointHistory::query()
            ->whereNull('semester_id')
            ->update(['semester_id' => $semester->id]);

        $this->cachedActiveId = $semester->id;

        return $semester;
    }
}
