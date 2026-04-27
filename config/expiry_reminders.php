<?php

$windows = array_values(array_filter(array_map(
    static fn (string $day): int => (int) trim($day),
    explode(',', (string) env('EXPIRY_REMINDER_DAYS', '90,60,30'))
), static fn (int $day): bool => $day >= 0));

if (empty($windows)) {
    $windows = [90, 60, 30];
}

rsort($windows);

return [
    'windows' => $windows,
    'mail_schedule_time' => env('EXPIRY_REMINDER_SCHEDULE_TIME', '08:00'),
];
