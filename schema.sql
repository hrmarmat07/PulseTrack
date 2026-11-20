CREATE DATABASE pulsetrack;
USE pulsetrack;

CREATE TABLE users (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    mobile_no VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(120),
    date_of_birth DATE,
    gender VARCHAR(20),
    height_cm DECIMAL(5,2),
    weight_kg DECIMAL(5,2),
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME
);

CREATE TABLE reference_range (
    range_id INT PRIMARY KEY,
    parameter VARCHAR(30) NOT NULL UNIQUE,
    description VARCHAR(150),
    unit VARCHAR(20),
    min_value DECIMAL(10,2),
    max_value DECIMAL(10,2)
);

CREATE TABLE health_reading (
    reading_id INT PRIMARY KEY,
    user_id INT NOT NULL,
    reading_time DATETIME NOT NULL,
    systolic DECIMAL(6,2),
    diastolic DECIMAL(6,2),
    heart_rate DECIMAL(6,2),
    resting_hr DECIMAL(6,2),
    spo2 DECIMAL(5,2),
    body_temp DECIMAL(5,2),
    source VARCHAR(30),

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

CREATE TABLE health_alert (
    alert_id INT PRIMARY KEY,
    reading_id INT NOT NULL,
    range_id INT,
    parameter VARCHAR(30) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    message VARCHAR(255) NOT NULL,
    severity VARCHAR(20) NOT NULL,
    alert_time DATETIME,

    FOREIGN KEY (reading_id)
    REFERENCES health_reading(reading_id)
    ON DELETE CASCADE,

    FOREIGN KEY (range_id)
    REFERENCES reference_range(range_id)
);

CREATE TABLE activity_type (
    activity_type_id INT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL UNIQUE,
    met_value DECIMAL(5,2)
);

CREATE TABLE activity_session (
    session_id INT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    duration_sec INT,
    distance_m DECIMAL(10,2),
    calories_kcal DECIMAL(10,2),

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE,

    FOREIGN KEY (activity_type_id)
    REFERENCES activity_type(activity_type_id)
);

CREATE TABLE gps_point (
    point_id INT PRIMARY KEY,
    session_id INT NOT NULL,
    recorded_at DATETIME NOT NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,

    FOREIGN KEY (session_id)
    REFERENCES activity_session(session_id)
    ON DELETE CASCADE
);

CREATE TABLE sleep_records (
    user_id INT NOT NULL,
    sleep_date DATE NOT NULL,
    total_sleep_mins INT,
    light_sleep_mins INT,
    deep_sleep_mins INT,
    rem_sleep_mins INT,
    awake_mins INT,
    sleep_score INT,

    PRIMARY KEY (user_id, sleep_date),

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

CREATE TABLE daily_summary (
    user_id INT NOT NULL,
    record_date DATE NOT NULL,
    step_count INT,
    active_calories DECIMAL(10,2),
    total_calories DECIMAL(10,2),
    water_take_ml INT,
    active_minutes INT,

    PRIMARY KEY (user_id, record_date),

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

INSERT INTO reference_range
VALUES
(1, 'heart_rate', 'General resting adult reference', 'bpm', 60, 100),
(2, 'spo2', 'Typical reference range', '%', 95, 100),
(3, 'body_temp', 'General reference', 'Celsius', 36.1, 37.2),
(4, 'systolic', 'Example reference range', 'mmHg', 90, 120),
(5, 'diastolic', 'Example reference range', 'mmHg', 60, 80);

INSERT INTO activity_type
VALUES
(1, 'Walking', 3.5),
(2, 'Running', 8.0),
(3, 'Cycling', 6.0),
(4, 'Workout', 5.0),
(5, 'Other', 3.0);

CREATE VIEW member_health_report AS
SELECT
    U.user_id,
    U.full_name,
    H.reading_time,
    H.systolic,
    H.diastolic,
    H.heart_rate,
    H.resting_hr,
    H.spo2,
    H.body_temp
FROM users U
INNER JOIN health_reading H
ON U.user_id = H.user_id;

CREATE VIEW admin_summary AS
SELECT
    U.user_id,
    U.full_name,
    COUNT(H.reading_id) AS total_readings,
    AVG(H.heart_rate) AS avg_heart_rate
FROM users U
LEFT JOIN health_reading H
ON U.user_id = H.user_id
GROUP BY U.user_id, U.full_name;

CREATE VIEW alerts_view AS
SELECT
    A.alert_id,
    H.user_id,
    A.reading_id,
    A.parameter,
    A.value,
    A.message,
    A.severity,
    A.alert_time
FROM health_alert A
INNER JOIN health_reading H
ON A.reading_id = H.reading_id;

CREATE VIEW daily_activity_view AS
SELECT
    U.user_id,
    U.full_name,
    D.record_date,
    D.step_count,
    D.active_calories,
    D.total_calories,
    D.water_take_ml,
    D.active_minutes
FROM users U
INNER JOIN daily_summary D
ON U.user_id = D.user_id;

SHOW TABLES;

SELECT * FROM users;

SELECT * FROM reference_range;

SELECT * FROM health_reading;

SELECT * FROM health_alert;

SELECT * FROM activity_type;

SELECT * FROM activity_session;

SELECT * FROM gps_point;

SELECT * FROM sleep_records;

SELECT * FROM daily_summary;

SELECT * FROM member_health_report;

SELECT * FROM admin_summary;

SELECT * FROM alerts_view;

SELECT * FROM daily_activity_view;