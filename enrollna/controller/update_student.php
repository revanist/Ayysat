<?php
require_once __DIR__ . '/../functions/admin_auth.php';
require_admin_login();
require_admin_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/students.php', true, 303);
    exit;
}

include '../db/dbconn.php';

$id          = (int) ($_POST['id'] ?? 0);
$first_name  = trim((string) ($_POST['first_name'] ?? ''));
$middle_name = trim((string) ($_POST['middle_name'] ?? ''));
$last_name   = trim((string) ($_POST['last_name'] ?? ''));
$email       = trim((string) ($_POST['email'] ?? ''));
$address     = trim((string) ($_POST['address'] ?? ''));
$contact     = trim((string) ($_POST['contact'] ?? ''));

if ($id < 1 || $first_name === '' || $last_name === '') {
    http_response_code(400);
    exit('Invalid student update parameters.');
}

$stmt = mysqli_prepare($conn, '
    UPDATE students SET
        first_name = ?,
        middle_name = ?,
        last_name = ?,
        email = ?,
        address = ?,
        contact = ?
    WHERE id = ?
');
mysqli_stmt_bind_param($stmt, 'ssssssi', $first_name, $middle_name, $last_name, $email, $address, $contact, $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: ../admin/students.php', true, 303);
    exit;
}

http_response_code(500);
echo 'Update failed. Please try again.';
