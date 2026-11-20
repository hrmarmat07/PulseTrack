<?php
require_once __DIR__ . '/common.php';
require_login();

$pdo = db();
$uid = (int)$_SESSION['user_id'];
$d   = get_json_input();

$type  = $d['type'] ?? '';
$value = to_number($d['value'] ?? null);
$time  = str_replace('T', ' ', $d['reading_time'] ?? date('Y-m-d H:i:s'));

if ($type === 'heart_rate' && ($value === null || $value < 20 || $value > 250)) {
    send_json(['success' => false, 'message' => 'Invalid heart rate.'], 400);
}
if ($type === 'spo2' && ($value === null || $value < 70 || $value > 100)) {
    send_json(['success' => false, 'message' => 'Invalid SpO2 value.'], 400);
}
if ($type !== 'heart_rate' && $type !== 'spo2') {
    send_json(['success' => false, 'message' => 'Invalid vital type.'], 400);
}

try {
    $col = ($type === 'heart_rate') ? 'heart_rate' : 'spo2';
    $stmt = $pdo->prepare("INSERT INTO health_reading (user_id, reading_time, $col, source) VALUES (?, ?, ?, 'watch_ble')");
    $stmt->execute([$uid, $time, $value]);
    $readingId = (int)$pdo->lastInsertId();

    $alert = $pdo->prepare("
        INSERT INTO health_alert (reading_id, range_id, parameter, value, message, severity)
        SELECT ?, range_id, ?, ?, CONCAT(?, ' outside normal range: ', ?, ' ', unit), 'warning'
        FROM reference_range
        WHERE parameter = ? AND (? < min_value OR ? > max_value)
    ");
    $alert->execute([$readingId, $type, $value, $type, $value, $type, $value, $value]);

    send_json(['success' => true, 'reading_id' => $readingId]);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 500);
}