<?php
/** Record a student's choice to pay at the cashier without marking it paid. */
require_once __DIR__ . '/student_auth.php';
require_student_login();
require_student_csrf();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

require __DIR__ . '/../db/dbconn.php';

$userId = (int) $_SESSION['user_id'];
$cashAmount = (float) ($_POST['cash_amount'] ?? 0);
if ($cashAmount <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Enter a valid cash payment amount.']);
    exit;
}

$stmt = mysqli_prepare($conn, "
    SELECT enrollments.id, students.remaining_balance
    FROM enrollments
    INNER JOIN students ON students.id = enrollments.student_id
    WHERE students.user_id = ?
      AND enrollments.STATUS = 'approved'
      AND enrollments.payment_status != 'Paid'
    ORDER BY enrollments.id DESC
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$enrollment = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$enrollment) {
    http_response_code(400);
    echo json_encode(['error' => 'No approved unpaid enrollment was found.']);
    exit;
}

$enrollmentId = (int) $enrollment['id'];
$remainingBalance = (float) $enrollment['remaining_balance'];
if ($cashAmount > $remainingBalance) {
    http_response_code(400);
    echo json_encode(['error' => 'The cash amount cannot exceed your remaining balance.']);
    exit;
}

$update = mysqli_prepare($conn, "
    UPDATE enrollments
    SET payment_method = 'cash', payment_option = 'cash', payment_reference = NULL,
        cash_amount_requested = ?
    WHERE id = ?
");
mysqli_stmt_bind_param($update, 'di', $cashAmount, $enrollmentId);
mysqli_stmt_execute($update);
mysqli_stmt_close($update);

echo json_encode(['message' => 'Cash payment request recorded. Please pay the cashier to complete enrollment.']);
