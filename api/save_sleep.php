<?php
require_once __DIR__ . '/common.php';
require_login();

$pdo = db();
$uid = (int)$_SESSION['user_id'];
$d   = get_json_input();

try {
    $stmt = $pdo->prepare("
        INSERT INTO sleep_records
            (user_id, sleep_date, total_sleep_mins, light_sleep_mins, deep_sleep_mins, rem_sleep_mins, awake_mins, sleep_score)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            total_sleep_mins = VALUES(total_sleep_mins),
            light_sleep_mins = VALUES(light_sleep_mins),
            deep_sleep_mins  = VALUES(deep_sleep_mins),
            rem_sleep_mins   = VALUES(rem_sleep_mins),
            awake_mins       = VALUES(awake_mins),
            sleep_score      = VALUES(sleep_score)
    ");
    $stmt->execute([
        $uid,
        $d['sleep_date'],
        (int)($d['total_sleep_mins'] ?? 0),
        (int)($d['light_sleep_mins'] ?? 0),
        (int)($d['deep_sleep_mins'] ?? 0),
        (int)($d['rem_sleep_mins'] ?? 0),
        (int)($d['awake_mins'] ?? 0),
        to_number($d['sleep_score'] ?? null)
    ]);

    send_json(['success' => true, 'message' => 'Sleep record saved.']);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 500);
}