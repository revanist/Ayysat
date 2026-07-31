<?php
/**
 * Toggle a subject's availability status (Admin only, POST + CSRF).
 */
require_once __DIR__ . '/admin_auth.php';
require_admin_login();
require_admin_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/courses.php', true, 303);
    exit;
}

include '../db/dbconn.php';

$subject_id = (int) ($_POST['subject_id'] ?? 0);
$course_id  = (int) ($_POST['course_id']  ?? 0);

if ($subject_id < 1) {
    http_response_code(400);
    exit('Invalid subject.');
}

// Toggle: flip the current value
$stmt = mysqli_prepare($conn,
    'UPDATE subjects SET is_available = IF(is_available = 1, 0, 1) WHERE id = ?'
);
mysqli_stmt_bind_param($stmt, 'i', $subject_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$redirect = '../admin/courses.php' . ($course_id > 0 ? '?course=' . $course_id : '');
header('Location: ' . $redirect, true, 303);
exit;
