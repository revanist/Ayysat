<?php
/**
 * Approve or reject an enrollment application.
 * Admin-only. Requires POST + CSRF token.
 */

require_once __DIR__ . '/admin_auth.php';
require_admin_login();
require_admin_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/enrollments.php', true, 303);
    exit;
}

include '../db/dbconn.php';

$id     = (int) ($_POST['enrollment_id'] ?? 0);
$status = strtolower(trim((string) ($_POST['status'] ?? '')));

if ($id < 1 || !in_array($status, ['approved', 'rejected'], true)) {
    http_response_code(400);
    exit('Invalid request parameters.');
}

$stmt = mysqli_prepare($conn, 'UPDATE enrollments SET STATUS = ? WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'si', $status, $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: ../admin/enrollments.php', true, 303);
    exit;
}

http_response_code(500);
echo 'Failed to update enrollment. Please try again.';
