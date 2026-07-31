<?php
require_once __DIR__ . '/session_security.php';

/** Shared session and access controls for student-only pages and actions. */
function start_student_session(): void
{
    start_secure_session();
}

function require_student_login(): void
{
    start_student_session();

    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
        header('Location: ../auth/login.php', true, 303);
        exit;
    }
}

function student_csrf_token(): string
{
    if (empty($_SESSION['student_csrf_token'])) {
        $_SESSION['student_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['student_csrf_token'];
}

function require_student_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['student_csrf_token']) || !hash_equals($_SESSION['student_csrf_token'], $token)) {
        http_response_code(403);
        exit('Invalid form request. Please return to your profile and try again.');
    }
}
