<?php
session_start();
require "../db/dbconn.php";

if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pending_enrollments.php");
    exit();
}

$student_id = intval($_POST['student_id']);

if ($student_id <= 0) {
    die("Invalid student.");
}

mysqli_begin_transaction($conn);

try {

    $year = date('Y');

    // Lock rows while generating the next student number
    $sql = "
        SELECT student_number
        FROM students
        WHERE student_number LIKE CONCAT(?, '-%')
        ORDER BY student_number DESC
        LIMIT 1
        FOR UPDATE
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $year);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        $parts = explode('-', $row['student_number']);
        $next = intval($parts[1]) + 1;
    } else {

        $next = 1;
    }

    mysqli_stmt_close($stmt);

    $student_number = $year . "-" . str_pad($next, 3, "0", STR_PAD_LEFT);

    // Update student
    $stmt = mysqli_prepare($conn, "
        UPDATE students
        SET
            student_number = ?,
            enrollment_status = 'Enrolled'
        WHERE id = ?
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $student_number,
        $student_id
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Update enrollment
    $stmt = mysqli_prepare($conn, "
        UPDATE enrollments
        SET status = 'Enrolled'
        WHERE student_id = ?
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $student_id
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($conn);

    $_SESSION['success'] =
        "Student approved successfully. Student Number: " . $student_number;
} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error'] = $e->getMessage();
}

header("Location: pending_enrollments.php");
exit();
