<?php
declare(strict_types=1);

function membership_max_classes(string $plan): ?int {
    return match($plan) {
        'offpeak' => 2,
        'classic' => 6,
        'premium' => null,
        default   => 0,
    };
}

function membership_can_enroll(PDO $db, int $userId, string $plan): bool {
    $max = membership_max_classes($plan);

    if ($max === 0)    return false;
    if ($max === null) return true;

    $stmt = $db->prepare('SELECT COUNT(*) FROM enrollments WHERE user_id = ?');
    $stmt->execute([$userId]);

    return (int)$stmt->fetchColumn() < $max;
}
