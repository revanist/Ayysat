<?php
/**
 * PayMongo Webhook Handler
 *
 * Receives payment.paid events from PayMongo and automatically
 * marks the matching enrollment as Paid in the database.
 *
 * Register this URL in your PayMongo Dashboard → Webhooks:
 *   http://yourdomain.com/enrollna/functions/paymongo_webhook.php
 *
 * Events to listen for: payment.paid
 */

require_once __DIR__ . '/../config/paymongo.php';
include __DIR__ . '/../db/dbconn.php';

// ── 1. Read raw body before any output ──────────────────────────────────────
$rawBody   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';

// ── 2. Validate webhook signature ───────────────────────────────────────────
// PayMongo sends:  t=<timestamp>,te=<test_sig>,li=<live_sig>
$parts = [];
foreach (explode(',', $signature) as $part) {
    [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
    $parts[$k] = $v;
}

$timestamp = $parts['t']  ?? '';
// Use 'te' for test-mode webhooks, 'li' for live-mode
$incoming  = $parts['te'] ?? $parts['li'] ?? '';

if ($timestamp === '' || $incoming === '') {
    http_response_code(400);
    exit('Missing signature.');
}

// Re-compute expected HMAC
$signedPayload = $timestamp . '.' . $rawBody;
$expected      = hash_hmac('sha256', $signedPayload, PAYMONGO_WEBHOOK_SECRET);

if (!hash_equals($expected, $incoming)) {
    http_response_code(401);
    exit('Invalid signature.');
}

// ── 3. Replay-attack guard (reject events older than 5 minutes) ─────────────
if (abs(time() - (int)$timestamp) > 300) {
    http_response_code(400);
    exit('Event too old.');
}

// ── 4. Parse event ──────────────────────────────────────────────────────────
$event = json_decode($rawBody, true);
$type  = $event['data']['attributes']['type'] ?? '';

if ($type !== 'payment.paid') {
    // Other events — acknowledge without action
    http_response_code(200);
    exit('OK');
}

// ── 5. Extract the payment_link_id from the payment resource ────────────────
$payment    = $event['data']['attributes']['data'] ?? [];
$linkId     = $payment['attributes']['payment_intent_id']
           ?? $payment['attributes']['source']['id']
           ?? null;

// PayMongo Payment Links put the link ID in payment.attributes.payment_link_id
$linkId = $payment['attributes']['payment_link_id'] ?? $linkId;

if (!$linkId) {
    // Try alternate location
    $linkId = $event['data']['attributes']['data']['id'] ?? null;
}

if (!$linkId) {
    http_response_code(422);
    exit('Cannot determine payment link ID.');
}

// ── 6. Update the database ──────────────────────────────────────────────────
mysqli_begin_transaction($conn);
try {
    // Mark the enrollment as Paid
    $upd = mysqli_prepare($conn,
        "UPDATE enrollments
         SET payment_status = 'Paid'
         WHERE paymongo_link_id = ?
           AND payment_status != 'Paid'"
    );
    mysqli_stmt_bind_param($upd, 's', $linkId);
    mysqli_stmt_execute($upd);
    $affected = mysqli_stmt_affected_rows($upd);
    mysqli_stmt_close($upd);

    if ($affected > 0) {
        // Zero out the student's remaining balance
        $bal = mysqli_prepare($conn,
            "UPDATE students
             SET remaining_balance = 0
             WHERE id = (
                 SELECT student_id FROM enrollments WHERE paymongo_link_id = ? LIMIT 1
             )"
        );
        mysqli_stmt_bind_param($bal, 's', $linkId);
        mysqli_stmt_execute($bal);
        mysqli_stmt_close($bal);
    }

    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    exit('Database error.');
}

http_response_code(200);
echo 'OK';
