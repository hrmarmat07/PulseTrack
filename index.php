<?php
require_once __DIR__ . '/api/config.php';
session_start();

$error = '';
$mode = $_POST['action'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile   = trim($_POST['mobile_no'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        if ($mode === 'register') {
            $name = trim($_POST['full_name'] ?? '');
            if ($name === '' || $mobile === '' || strlen($password) < 6) {
                throw new Exception('Fill all fields (password min 6 characters).');
            }
            $stmt = db()->prepare('INSERT INTO users (full_name, mobile_no, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $mobile, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            $_SESSION['full_name'] = $name;
            header('Location: dashboard.php');
            exit;
        } else {
            $stmt = db()->prepare('SELECT user_id, full_name, password_hash FROM users WHERE mobile_no = ?');
            $stmt->execute([$mobile]);
            $user = $stmt->fetch();
            if (!$user || !password_verify($password, $user['password_hash'])) {
                throw new Exception('Invalid mobile number or password.');
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            header('Location: dashboard.php');
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | PulseTrack</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<div class="auth">
    <aside class="auth-side">
        <div class="wordmark">Pulse<b>Track</b></div>
        <div>
            <h1>PulseTrack</h1>
            <p>Track heart rate, SpO₂, daily activity, and sleep records.</p>
        </div>
        <p class="side-footer">Smartwatch Health Monitoring System</p>
    </aside>

    <main class="auth-main">
        <div class="auth-box">
            <h2>Welcome</h2>
            <p class="muted">Sign in or create an account.</p>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="tabs">
                <button type="button" id="tabLogin" class="<?= $mode === 'register' ? '' : 'active' ?>">Sign in</button>
                <button type="button" id="tabRegister" class="<?= $mode === 'register' ? 'active' : '' ?>">Create account</button>
            </div>

            <form method="post" id="loginForm" class="<?= $mode === 'register' ? '' : 'active' ?>">
                <div class="field">
                    <label for="login_mobile">Mobile number</label>
                    <input id="login_mobile" name="mobile_no" inputmode="numeric" required>
                </div>
                <div class="field">
                    <label for="login_password">Password</label>
                    <input id="login_password" type="password" name="password" required>
                </div>
                <input type="hidden" name="action" value="login">
                <button type="submit">Sign in</button>
            </form>

            <form method="post" id="registerForm" class="<?= $mode === 'register' ? 'active' : '' ?>">
                <div class="field">
                    <label for="reg_name">Full name</label>
                    <input id="reg_name" name="full_name" required>
                </div>
                <div class="field">
                    <label for="reg_mobile">Mobile number</label>
                    <input id="reg_mobile" name="mobile_no" inputmode="numeric" required>
                </div>
                <div class="field">
                    <label for="reg_password">Password (min 6 characters)</label>
                    <input id="reg_password" type="password" name="password" minlength="6" required>
                </div>
                <input type="hidden" name="action" value="register">
                <button type="submit">Create account</button>
            </form>
        </div>
    </main>
</div>

<script src="assets/js/auth.js"></script>
</body>
</html>