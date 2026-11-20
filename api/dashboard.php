<?php
require_once __DIR__ . '/common.php';
require_login();

$pdo = db();
$uid = (int)$_SESSION['user_id'];

try {
    // 1. Latest heart rate
    $latestHr = $pdo->prepare("
        SELECT heart_rate
        FROM health_reading
        WHERE user_id = ?
          AND heart_rate IS NOT NULL
        ORDER BY reading_time DESC
        LIMIT 1
    ");
    $latestHr->execute([$uid]);

    // 2. Latest SpO2
    $latestSpo2 = $pdo->prepare("
        SELECT spo2
        FROM health_reading
        WHERE user_id = ?
          AND spo2 IS NOT NULL
        ORDER BY reading_time DESC
        LIMIT 1
    ");
    $latestSpo2->execute([$uid]);

    // 3. Today's steps
    $todaySteps = $pdo->prepare("
        SELECT COALESCE(step_count, 0)
        FROM daily_summary
        WHERE user_id = ?
          AND record_date = CURDATE()
    ");
    $todaySteps->execute([$uid]);

    // 4. Today's sleep
    $todaySleep = $pdo->prepare("
        SELECT COALESCE(total_sleep_mins, 0)
        FROM sleep_records
        WHERE user_id = ?
          AND sleep_date = CURDATE()
    ");
    $todaySleep->execute([$uid]);

    // 5. Heart rate history (last 14 days)
    $heartRecords = $pdo->prepare("
        SELECT reading_time, heart_rate
        FROM health_reading
        WHERE user_id = ?
          AND heart_rate IS NOT NULL
          AND reading_time >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
        ORDER BY reading_time DESC
    ");
    $heartRecords->execute([$uid]);

    // 6. SpO2 history (last 14 days)
    $spo2Records = $pdo->prepare("
        SELECT reading_time, spo2
        FROM health_reading
        WHERE user_id = ?
          AND spo2 IS NOT NULL
          AND reading_time >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
        ORDER BY reading_time DESC
    ");
    $spo2Records->execute([$uid]);

    // 7. Daily activity history (last 14 entries)
    $activityRecords = $pdo->prepare("
        SELECT
            record_date,
            step_count,
            active_calories,
            total_calories,
            water_take_ml,
            active_minutes
        FROM daily_summary
        WHERE user_id = ?
        ORDER BY record_date DESC
        LIMIT 14
    ");
    $activityRecords->execute([$uid]);

    // 8. Sleep history (last 14 entries)
    $sleepRecords = $pdo->prepare("
        SELECT
            sleep_date,
            total_sleep_mins,
            light_sleep_mins,
            deep_sleep_mins,
            rem_sleep_mins,
            awake_mins,
            sleep_score
        FROM sleep_records
        WHERE user_id = ?
        ORDER BY sleep_date DESC
        LIMIT 14
    ");
    $sleepRecords->execute([$uid]);

    // 9. Recent health alerts
    $alerts = $pdo->prepare("
        SELECT
            parameter,
            value,
            message,
            severity,
            alert_time
        FROM alerts_view
        WHERE user_id = ?
        ORDER BY alert_time DESC
        LIMIT 10
    ");
    $alerts->execute([$uid]);

    send_json([
        'success'          => true,
        'latest_hr'        => $latestHr->fetchColumn(),
        'latest_spo2'      => $latestSpo2->fetchColumn(),
        'today_steps'      => $todaySteps->fetchColumn() ?: 0,
        'today_sleep'      => $todaySleep->fetchColumn() ?: 0,
        'heart_records'    => $heartRecords->fetchAll(),
        'spo2_records'     => $spo2Records->fetchAll(),
        'activity_records' => $activityRecords->fetchAll(),
        'sleep_records'    => $sleepRecords->fetchAll(),
        'alerts'           => $alerts->fetchAll()
    ]);

} catch (Throwable $e) {
    send_json([
        'success' => false,
        'message' => $e->getMessage()
    ], 500);
}