<?php

return [
    'starting_points' => (int) env('DISCIPLINE_STARTING_POINTS', 100),
    // Уведомление, если баланс строго ниже этого значения (при 21 → когда осталось 20 или меньше)
    'low_points_threshold' => (int) env('DISCIPLINE_LOW_POINTS_THRESHOLD', 21),
];
