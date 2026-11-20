<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$username = htmlspecialchars($_SESSION['full_name'] ?? 'Member');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Records | PulseTrack</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/records.css">
</head>
<body>

<header class="nav">
    <div class="nav-inner">
        <a class="wordmark" href="dashboard.php">Pulse<b>Track</b></a>
        <nav class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="records.php" class="current">Records</a>
        </nav>
        <div class="nav-user">
            <span><?= $username ?></span>
            <a href="logout.php">Log out</a>
        </div>
    </div>
</header>

<div class="jump">
    <div class="jump-inner">
        <a href="#vitals">Heart rate and SpO₂</a>
        <a href="#activity">Daily activity</a>
        <a href="#sleep">Sleep</a>
        <a href="#alerts-section">Alerts</a>
    </div>
</div>

<main class="page">
    <div class="page-title">
        <h1>Records</h1>
    </div>

    <section class="block" id="vitals">
        <h2>Heart rate and SpO₂</h2>
        <p class="hint">Last 14 days</p>
        <div class="two-col">
            <div class="panel">
                <h3>Heart rate</h3>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>Date and time</th><th>Heart rate</th></tr></thead>
                        <tbody id="heartTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <h3>SpO₂</h3>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>Date and time</th><th>SpO₂</th></tr></thead>
                        <tbody id="spo2TableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="block" id="activity">
        <h2>Daily activity</h2>
        <p class="hint">Existing dates will be updated.</p>
        <div class="panel">
            <form id="dailyForm">
                <div class="form-grid">
                    <div class="field"><label for="record_date">Date</label><input id="record_date" type="date" name="record_date" required></div>
                    <div class="field"><label for="step_count">Steps</label><input id="step_count" type="number" name="step_count" min="0" required></div>
                    <div class="field"><label for="active_calories">Active calories</label><input id="active_calories" type="number" name="active_calories" min="0"></div>
                    <div class="field"><label for="total_calories">Total calories</label><input id="total_calories" type="number" name="total_calories" min="0"></div>
                    <div class="field"><label for="water_take_ml">Water intake (ml)</label><input id="water_take_ml" type="number" name="water_take_ml" min="0"></div>
                    <div class="field"><label for="active_minutes">Active minutes</label><input id="active_minutes" type="number" name="active_minutes" min="0"></div>
                </div>
                <button type="submit">Save activity</button>
            </form>
        </div>

        <div class="panel">
            <h3>Activity history</h3>
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Date</th><th>Steps</th><th>Active cal</th><th>Total cal</th><th>Water (ml)</th><th>Active min</th></tr></thead>
                    <tbody id="activityTableBody"></tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="block" id="sleep">
        <h2>Sleep</h2>
        <p class="hint">Enter values in minutes.</p>
        <div class="panel">
            <form id="sleepForm">
                <div class="form-grid">
                    <div class="field"><label for="sleep_date">Date</label><input id="sleep_date" type="date" name="sleep_date" required></div>
                    <div class="field"><label for="total_sleep_mins">Total sleep (min)</label><input id="total_sleep_mins" type="number" name="total_sleep_mins" min="0"></div>
                    <div class="field"><label for="sleep_score">Sleep score (0-100)</label><input id="sleep_score" type="number" name="sleep_score" min="0" max="100"></div>
                    <div class="field"><label for="light_sleep_mins">Light sleep (min)</label><input id="light_sleep_mins" type="number" name="light_sleep_mins" min="0"></div>
                    <div class="field"><label for="deep_sleep_mins">Deep sleep (min)</label><input id="deep_sleep_mins" type="number" name="deep_sleep_mins" min="0"></div>
                    <div class="field"><label for="rem_sleep_mins">REM sleep (min)</label><input id="rem_sleep_mins" type="number" name="rem_sleep_mins" min="0"></div>
                    <div class="field"><label for="awake_mins">Awake time (min)</label><input id="awake_mins" type="number" name="awake_mins" min="0"></div>
                </div>
                <button type="submit">Save sleep</button>
            </form>
        </div>

        <div class="panel">
            <h3>Sleep history</h3>
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Date</th><th>Total</th><th>Light</th><th>Deep</th><th>REM</th><th>Awake</th><th>Score</th></tr></thead>
                    <tbody id="sleepTableBody"></tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="block" id="alerts-section">
        <h2>Health alerts</h2>
        <p class="hint">Readings outside normal range.</p>
        <div id="alerts">Loading alerts...</div>
    </section>
</main>

<script src="assets/js/api.js"></script>
<script src="assets/js/records.js"></script>
</body>
</html>