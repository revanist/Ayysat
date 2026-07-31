<?php
require_once __DIR__ . '/../functions/admin_auth.php';
require_admin_login();
require_admin_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/students.php', true, 303);
    exit;
}

require __DIR__ . '/../db/dbconn.php';

$studentId = (int) ($_POST['id'] ?? 0);
if ($studentId < 1) {
    http_response_code(400);
    exit('Invalid student ID.');
}

mysqli_begin_transaction($conn);

try {
    // Enrollment details must be deleted before their parent enrollment rows.
    $details = mysqli_prepare($conn, '
        DELETE enrollment_details
        FROM enrollment_details
        INNER JOIN enrollments ON enrollments.id = enrollment_details.enrollment_id
        WHERE enrollments.student_id = ?
    ');
    mysqli_stmt_bind_param($details, 'i', $studentId);
    mysqli_stmt_execute($details);
    mysqli_stmt_close($details);

    $enrollments = mysqli_prepare($conn, 'DELETE FROM enrollments WHERE student_id = ?');
    mysqli_stmt_bind_param($enrollments, 'i', $studentId);
    mysqli_stmt_execute($enrollments);
    mysqli_stmt_close($enrollments);

    $student = mysqli_prepare($conn, 'DELETE FROM students WHERE id = ?');
    mysqli_stmt_bind_param($student, 'i', $studentId);
    mysqli_stmt_execute($student);
    $deleted = mysqli_stmt_affected_rows($student);
    mysqli_stmt_close($student);

    if ($deleted !== 1) {
        throw new RuntimeException('Student not found.');
    }

    mysqli_commit($conn);
    header('Location: ../admin/students.php?deleted=1', true, 303);
    exit;
} catch (Throwable $exception) {
    mysqli_rollback($conn);
    http_response_code(500);
    exit('Unable to delete the student.');
}
