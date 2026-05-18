<?php

namespace App\Services;

use App\Models\DisciplineRule;
use App\Models\PointHistory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointService
{
    public function __construct(
        private NotificationService $notificationService,
        private SemesterService $semesterService,
    ) {}

    public function assignPoints(
        array $studentIds,
        array $ruleIds,
        string $comment,
        User $actorUser,
        string $actorRole = 'admin',
    ): void
    {
        $studentIds = array_values(array_unique(array_map('intval', $studentIds)));
        $ruleIds = array_values(array_unique(array_map('intval', $ruleIds)));

        if ($studentIds === [] || $ruleIds === []) {
            return;
        }

        DB::transaction(function () use ($studentIds, $ruleIds, $comment, $actorUser, $actorRole): void {
            $students = Student::query()
                ->with('user')
                ->whereIn('id', $studentIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $rules = DisciplineRule::query()
                ->whereIn('id', $ruleIds)
                ->where('is_active', true)
                ->get();

            if ($students->isEmpty() || $rules->isEmpty()) {
                return;
            }

            foreach ($students as $student) {
                foreach ($rules as $rule) {
                    $before = (int) $student->current_points;
                    $delta = (int) $rule->points;
                    $after = $before + $delta;

                    $student->current_points = $after;
                    $student->save();

                    $history = PointHistory::query()->create([
                        'semester_id' => $this->semesterService->activeSemesterId(),
                        'student_id' => $student->id,
                        'rule_id' => $rule->id,
                        'teacher_id' => $actorRole === 'teacher' || $actorRole === 'admin' ? $actorUser->id : null,
                        'points' => $delta,
                        'balance_before' => $before,
                        'balance_after' => $after,
                        'comment' => $comment,
                        'occurred_at' => now()->toDateString(),
                    ]);

                    // Уведомление, если баланс ниже порога (по умолчанию < 21)
                    $lowThreshold = (int) config('discipline.low_points_threshold', 21);

                    if ($delta < 0 && $after < $lowThreshold && $student->user) {
                        $this->notificationService->notifyLowBalanceForStudent(
                            studentUser: $student->user,
                            currentPoints: $after,
                            comment: $comment,
                        );

                        $admins = User::query()
                            ->where('role', 'admin')
                            ->where('is_active', true)
                            ->get();

                        foreach ($admins as $adminUser) {
                            $this->notificationService->notifyLowBalanceForAdmin(
                                adminUser: $adminUser,
                                studentUser: $student->user,
                                currentPoints: $after,
                                comment: $comment,
                            );
                        }
                    }
                }
            }
        });
    }
}
