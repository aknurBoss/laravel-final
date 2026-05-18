<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function notifyPointChangedForStudent(
        User $studentUser,
        int $deltaPoints,
        int $balanceBefore,
        int $balanceAfter,
        string $comment = '',
        ?int $ruleId = null,
        ?int $historyId = null,
        string $byRole = 'teacher',
        ?int $byUserId = null
    ): Notification {
        $type = $deltaPoints >= 0 ? 'point_added' : 'point_cancelled';

        $title = $deltaPoints >= 0
            ? 'Вам начислены баллы'
            : 'У вас списаны баллы';

        $sign = $deltaPoints > 0 ? '+' : '';
        $body = sprintf(
            'Изменение: %s%d, было: %d, стало: %d.%s',
            $sign,
            $deltaPoints,
            $balanceBefore,
            $balanceAfter,
            $comment ? ' Комментарий: ' . $comment : ''
        );

        return Notification::create([
            'user_id' => $studentUser->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => [
                'delta_points' => $deltaPoints,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'comment' => $comment,
                'rule_id' => $ruleId,
                'history_id' => $historyId,
                'by_role' => $byRole,
                'by_user_id' => $byUserId,
            ],
        ]);
    }

    public function notifyLowBalanceForAdmin(
        User $adminUser,
        User $studentUser,
        int $currentPoints,
        string $comment = ''
    ): Notification {
        $body = sprintf(
            'У ученика %s (%s) осталось %d баллов.%s',
            $studentUser->name,
            $studentUser->email,
            $currentPoints,
            $comment ? ' Комментарий: ' . $comment : ''
        );

        return Notification::create([
            'user_id' => $adminUser->id,
            'type' => 'student_low_points',
            'title' => 'Внимание: низкий баланс ученика',
            'body' => $body,
            'data' => [
                'student_user_id' => $studentUser->id,
                'current_points' => $currentPoints,
                'comment' => $comment,
            ],
        ]);
    }

    public function notifyLowBalanceForStudent(
        User $studentUser,
        int $currentPoints,
        string $comment = ''
    ): Notification {
        $threshold = (int) config('discipline.low_points_threshold', 21);
        $body = sprintf(
            'У вас осталось %d баллов (меньше %d). Вас могут вызвать к завучу.%s',
            $currentPoints,
            $threshold,
            $comment ? ' Комментарий: ' . $comment : ''
        );

        return Notification::create([
            'user_id' => $studentUser->id,
            'type' => 'low_points_warning',
            'title' => 'Предупреждение по баллам',
            'body' => $body,
            'data' => [
                'current_points' => $currentPoints,
                'comment' => $comment,
            ],
        ]);
    }
}
