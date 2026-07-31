<?php
require_once __DIR__ . '/student_auth.php';
require_student_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../student/profile.php', true, 303);
    exit;
}

require_student_csrf();
require '../db/dbconn.php';

$first_name  = trim((string) ($_POST['first_name'] ?? ''));
$middle_name = trim((string) ($_POST['middle_name'] ?? ''));
$last_name   = trim((string) ($_POST['last_name'] ?? ''));
$email       = trim((string) ($_POST['email'] ?? ''));
$contact     = trim((string) ($_POST['contact'] ?? ''));
$address     = trim((string) ($_POST['address'] ?? ''));
$user_id     = (int) $_SESSION['user_id'];

if ($first_name === '' || $last_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../student/profile.php?error=invalid_profile', true, 303);
    exit;
}

$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK || $_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
        header('Location: ../student/profile.php?error=invalid_picture', true, 303);
        exit;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['profile_picture']['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime]) || @getimagesize($_FILES['profile_picture']['tmp_name']) === false) {
        header('Location: ../student/profile.php?error=invalid_picture', true, 303);
        exit;
    }

    $upload_dir = __DIR__ . '/../uploads';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
        http_response_code(500);
        exit('Unable to save the profile picture.');
    }

    $profile_picture = 'student_' . $user_id . '_' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . DIRECTORY_SEPARATOR . $profile_picture)) {
        http_response_code(500);
        exit('Unable to save the profile picture.');
    }
}

// Update students table
if ($profile_picture !== null) {
    $stmt = mysqli_prepare($conn, 'UPDATE students SET first_name = ?, middle_name = ?, last_name = ?, email = ?, contact = ?, address = ?, profile_picture = ? WHERE user_id = ?');
    mysqli_stmt_bind_param($stmt, 'sssssssi', $first_name, $middle_name, $last_name, $email, $contact, $address, $profile_picture, $user_id);
} else {
    $stmt = mysqli_prepare($conn, 'UPDATE students SET first_name = ?, middle_name = ?, last_name = ?, email = ?, contact = ?, address = ? WHERE user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ssssssi', $first_name, $middle_name, $last_name, $email, $contact, $address, $user_id);
}
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Also sync users table (fullname and username/email)
$fullname = trim($first_name . ' ' . $last_name);
$user_stmt = mysqli_prepare($conn, 'UPDATE users SET fullname = ?, username = ? WHERE id = ?');
mysqli_stmt_bind_param($user_stmt, 'ssi', $fullname, $email, $user_id);
mysqli_stmt_execute($user_stmt);
mysqli_stmt_close($user_stmt);

$_SESSION['fullname'] = $fullname;
header('Location: ../student/profile.php?success=profile_updated', true, 303);
exit;
