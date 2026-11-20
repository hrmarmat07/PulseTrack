<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function send_json($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        send_json(['success' => false, 'message' => 'Please sign in again.'], 401);
    }
}

function get_json_input() {
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

function to_number($val) {
    return ($val === '' || $val === null || !is_numeric($val)) ? null : $val;
}