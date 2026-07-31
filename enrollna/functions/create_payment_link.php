<?php
/**
 * Creates a PayMongo Payment Link for the authenticated student.
 * Returns JSON: { url: "https://checkout.paymongo.com/..." }
 *
 * Requires: POST, student session, CSRF token.
 */

require_once __DIR__ . '/student_auth.php';
require_student_login();
require_student_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/paymongo.php';
include __DIR__ . '/../db/dbconn.php';

header('Content-Type: application/json');

if (str_contains(PAYMONGO_SECRET_KEY, 'REPLACE_WITH')) {
    http_response_code(503);
    echo json_encode(['error' => 'Online payments have not been configured yet. Please contact the administrator.']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Fetch the student's approved enrollment and remaining balance
$stmt = mysqli_prepare($conn, "
    SELECT
        students.id          AS student_id,
        students.first_name,
        students.last_name,
        students.email,
        students.remaining_balance,
        enrollments.id       AS enrollment_id,
        enrollments.STATUS,
        enrollments.payment_status
    FROM students
    INNER JOIN enrollments ON enrollments.student_id = students.id
    WHERE students.user_id = ?
      AND enrollments.STATUS = 'approved'
      AND enrollments.payment_status != 'Paid'
    ORDER BY enrollments.id DESC
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    http_response_code(400);
    echo json_encode(['error' => 'No approved unpaid enrollment found.']);
    exit;
}

$balance       = (float) $student['remaining_balance'];
$enrollmentId  = (int)   $student['enrollment_id'];
$amountCentavos = (int) round($balance * 100); // PayMongo uses centavos

if ($amountCentavos < 2000) { // PayMongo minimum is ₱20
    http_response_code(400);
    echo json_encode(['error' => 'Balance is below the minimum payable amount.']);
    exit;
}

$description = 'Enrollment Fee – '
    . htmlspecialchars($student['first_name'] . ' ' . $student['last_name']);

$payload = json_encode([
    'data' => [
        'attributes' => [
            'amount'       => $amountCentavos,
            'currency'     => 'PHP',
            'description'  => $description,
            'remarks'      => 'Enrollment ID: ' . $enrollmentId,
            'redirect'     => [
                'success' => SITE_BASE_URL . '/student/profile.php?payment=success',
                'failed'  => SITE_BASE_URL . '/student/profile.php?payment=failed',
            ],
        ],
    ],
]);

$ch = curl_init('https://api.paymongo.com/v1/payment_links');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':'),
    ],
]);

$response   = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpStatus !== 200 && $httpStatus !== 201) {
    http_response_code(502);
    echo json_encode(['error' => 'Payment gateway error. Please try again.']);
    exit;
}

$data       = json_decode($response, true);
$linkId     = $data['data']['id'] ?? null;
$checkoutUrl = $data['data']['attributes']['checkout_url'] ?? null;

if (!$linkId || !$checkoutUrl) {
    http_response_code(502);
    echo json_encode(['error' => 'Invalid response from payment gateway.']);
    exit;
}

// Store the link ID on the enrollment row so cashier can see it
$upd = mysqli_prepare($conn, 'UPDATE enrollments SET paymongo_link_id = ? WHERE id = ?');
mysqli_stmt_bind_param($upd, 'si', $linkId, $enrollmentId);
mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

echo json_encode(['url' => $checkoutUrl]);
