<?php
require_once __DIR__ . '/common.php';
require_login();

$pdo = db();
$uid = (int)$_SESSION['user_id'];
$d   = get_json_input();

try {
    $stmt = $pdo->prepare("
        INSERT INTO daily_summary
            (user_id, record_date, step_count, active_calories, total_calories, water_take_ml, active_minutes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            step_count      = VALUES(step_count),
            active_calories = VALUES(active_calories),
            total_calories  = VALUES(total_calories),
            water_take_ml   = VALUES(water_take_ml),
            active_minutes  = VALUES(active_minutes)
    ");
    $stmt->execute([
        $uid,
        $d['record_date'],
        (int)($d['step_count'] ?? 0),
        (float)($d['active_calories'] ?? 0),
        (float)($d['total_calories'] ?? 0),
        (int)($d['water_take_ml'] ?? 0),
        (int)($d['active_minutes'] ?? 0)
    ]);

    send_json(['success' => true, 'message' => 'Daily activity saved.']);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 500);
}