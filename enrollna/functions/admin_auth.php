<?php
require_once __DIR__ . '/session_security.php';

/**
 * Shared session and access controls for admin-only pages and actions.
 */

function start_admin_session(): void
{
    start_secure_session();
}

function require_admin_login(): void
{
    start_admin_session();

    if (empty($_SESSION['admin_id'])) {
        header('Location: ../auth/admin_login.php', true, 303);
        exit;
    }
}

function admin_csrf_token(): string
{
    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['admin_csrf_token'];
}

function require_admin_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (
        !is_string($token) ||
        empty($_SESSION['admin_csrf_token']) ||
        !hash_equals($_SESSION['admin_csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Invalid request. Please go back and try again.');
    }
}
