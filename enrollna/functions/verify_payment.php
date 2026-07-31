<?php
/**
 * Manual cashier payment verification (for cash / offline payments).
 * Admin-only. Requires POST + CSRF token.
 */

require_once __DIR__ . '/admin_auth.php';
require_admin_login();
require_admin_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/cashier.php', true, 303);
    exit;
}

include '../db/dbconn.php';

$id = (int) ($_POST['enrollment_id'] ?? 0);
if ($id < 1) {
    http_response_code(400);
    exit('Invalid enrollment ID.');
}

mysqli_begin_transaction($conn);

try {
    $lookup = mysqli_prepare($conn, "
        SELECT e.student_id, e.cash_amount_requested, s.remaining_balance
        FROM enrollments e
        INNER JOIN students s ON s.id = e.student_id
        WHERE e.id = ?
        FOR UPDATE
    ");
    mysqli_stmt_bind_param($lookup, 'i', $id);
    mysqli_stmt_execute($lookup);
    $payment = mysqli_fetch_assoc(mysqli_stmt_get_result($lookup));
    mysqli_stmt_close($lookup);

    if (!$payment) {
        throw new RuntimeException('Enrollment not found.');
    }

    $remaining = (float) $payment['remaining_balance'];
    $received = (float) ($payment['cash_amount_requested'] ?? 0);

    // Existing manual entries without a requested amount are treated as full payment.
    if ($received <= 0) {
        $received = $remaining;
    }

    $received = min($received, $remaining);
    $newBalance = max(0, $remaining - $received);
    $paymentStatus = $newBalance <= 0 ? 'Paid' : 'Pending';
    $studentId = (int) $payment['student_id'];

    $enrollmentUpdate = mysqli_prepare($conn, "
        UPDATE enrollments
        SET amount_paid = COALESCE(amount_paid, 0) + ?,
            cash_amount_requested = NULL,
            payment_status = ?
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($enrollmentUpdate, 'dsi', $received, $paymentStatus, $id);
    mysqli_stmt_execute($enrollmentUpdate);
    mysqli_stmt_close($enrollmentUpdate);

    $studentUpdate = mysqli_prepare($conn, '
        UPDATE students SET remaining_balance = ?, payment_status = ? WHERE id = ?
    ');
    mysqli_stmt_bind_param($studentUpdate, 'dsi', $newBalance, $paymentStatus, $studentId);
    mysqli_stmt_execute($studentUpdate);
    mysqli_stmt_close($studentUpdate);

    mysqli_commit($conn);
    header('Location: ../admin/cashier.php', true, 303);
    exit;
} catch (Throwable $exception) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo 'Payment verification failed. Please try again.';
}
