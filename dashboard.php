<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$name = htmlspecialchars($_SESSION['full_name'] ?? 'Member');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PulseTrack</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<header class="nav">
    <div class="nav-inner">
        <a class="wordmark" href="dashboard.php">Pulse<b>Track</b></a>
        <nav class="nav-links">
            <a href="dashboard.php" class="current">Dashboard</a>
            <a href="records.php">Records</a>
        </nav>
        <div class="nav-user">
            <span><?= $name ?></span>
            <a href="logout.php">Log out</a>
        </div>
    </div>
</header>

<main class="page">
    <div class="page-title">
        <h1>Welcome, <?= $name ?></h1>
        <p class="muted"><?= date('l, j F Y') ?></p>
    </div>

    <section class="watch-bar">
        <div class="watch-state">
            <span id="connectionDot" class="dot"></span>
            <div>
                <div id="connectionStatus">Watch not connected</div>
                <div id="connectedWatchName">No device</div>
            </div>
        </div>
        <button id="connectBtn" onclick="connectWatch()">Connect smartwatch</button>
    </section>

    <section class="vitals">
        <div class="vital heart">
            <div class="vital-name">Heart rate</div>
            <div class="vital-number"><strong id="heartRate">--</strong><span>bpm</span></div>
            <p class="vital-note">Latest reading</p>
        </div>
        <div class="vital oxygen">
            <div class="vital-name">Blood oxygen (SpO₂)</div>
            <div class="vital-number"><strong id="spo2Value">--</strong><span>%</span></div>
            <p class="vital-note">Latest reading</p>
        </div>
    </section>

    <section class="today">
        <div class="today-item">
            <span>Steps today</span>
            <strong id="stepsCard">0</strong>
        </div>
        <div class="today-item sleep">
            <span>Sleep last night</span>
            <strong id="sleepCard">0</strong><small>minutes</small>
        </div>
    </section>

    <section class="next-step">
        <p>View all readings, log daily activity or sleep, and check health alerts.</p>
        <a class="btn" href="records.php">Open records</a>
    </section>
</main>

<script src="assets/js/api.js"></script>
<script src="assets/js/dashboard.js"></script>
<script src="assets/js/bluetooth.js"></script>
</body>
</html>