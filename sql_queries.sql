USE pulsetrack;

SELECT
    AVG(heart_rate) AS Average_Heart_Rate,
    MIN(heart_rate) AS Minimum_Heart_Rate,
    MAX(heart_rate) AS Maximum_Heart_Rate
FROM health_reading
WHERE user_id = 1;

SELECT
    reading_time,
    heart_rate
FROM health_reading
WHERE user_id = 1
AND reading_time BETWEEN '2026-11-01 00:00:00'
                     AND '2026-11-14 23:59:59'
ORDER BY reading_time;

SELECT
    record_date,
    SUM(step_count) AS Total_Steps,
    SUM(active_calories) AS Total_Active_Calories,
    SUM(water_take_ml) AS Total_Water
FROM daily_summary
WHERE user_id = 1
GROUP BY record_date
ORDER BY record_date;

SELECT
    record_date,
    SUM(step_count) AS Total_Steps,
    SUM(active_calories) AS Total_Active_Calories,
    SUM(active_minutes) AS Total_Active_Minutes
FROM daily_summary
WHERE user_id = 1
GROUP BY record_date
ORDER BY record_date;

SELECT
    AVG(sleep_score) AS Average_Sleep_Score,
    AVG(total_sleep_mins) AS Average_Sleep_Minutes,
    MIN(total_sleep_mins) AS Minimum_Sleep_Minutes,
    MAX(total_sleep_mins) AS Maximum_Sleep_Minutes
FROM sleep_records
WHERE user_id = 1;


SELECT
    COUNT(session_id) AS Total_Workouts,
    SUM(distance_m) AS Total_Distance,
    SUM(calories_kcal) AS Total_Calories,
    SUM(duration_sec) AS Total_Duration
FROM activity_session
WHERE user_id = 1;

SELECT
    U.user_id,
    U.full_name,
    AVG(H.heart_rate) AS Average_Heart_Rate
FROM users U
LEFT JOIN health_reading H
ON U.user_id = H.user_id
GROUP BY U.user_id, U.full_name
ORDER BY U.user_id;

SELECT
    U.user_id,
    U.full_name,
    SUM(D.step_count) AS Total_Steps
FROM users U
LEFT JOIN daily_summary D
ON U.user_id = D.user_id
GROUP BY U.user_id, U.full_name
ORDER BY U.user_id;

SELECT *
FROM alerts_view
WHERE user_id = 1
ORDER BY alert_time DESC;

SELECT *
FROM member_health_report
WHERE user_id = 1
ORDER BY reading_time DESC;

SELECT *
FROM admin_summary;