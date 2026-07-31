<?php
session_start();
require "../db/dbconn.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../auth/forgot_password.php');
    exit();
}

$action = $_POST['action'] ?? 'verify_email';

if ($action === 'verify_email') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../auth/forgot_password.php?error=invalid_email');
        exit();
    }

    $stmt = mysqli_prepare($conn, "SELECT id, fullname FROM users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) !== 1) {
        mysqli_stmt_close($stmt);
        header('Location: ../auth/forgot_password.php?error=email_not_found');
        exit();
    }

    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    $_SESSION['password_reset_user_id'] = $user['id'];
    $_SESSION['password_reset_email'] = $email;

    header('Location: ../auth/forgot_password.php?step=set');
    exit();
}

if ($action === 'set_password') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $userId = $_SESSION['password_reset_user_id'] ?? null;

    if (empty($newPassword) || empty($confirmPassword)) {
        header('Location: ../auth/forgot_password.php?error=empty_password');
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        header('Location: ../auth/forgot_password.php?error=password_mismatch');
        exit();
    }

    if (strlen($newPassword) < 8) {
        header('Location: ../auth/forgot_password.php?error=shortpassword');
        exit();
    }

    if (!$userId) {
        header('Location: ../auth/forgot_password.php');
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($updateStmt, 'si', $hashedPassword, $userId);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);

    unset($_SESSION['password_reset_user_id'], $_SESSION['password_reset_email']);

    header('Location: ../auth/login.php?success=password_updated');
    exit();
}

header('Location: ../auth/forgot_password.php');
exit();
