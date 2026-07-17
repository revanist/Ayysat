<?php
session_start();
require "../db/dbconn.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../auth/admin_register.php");
    exit();
}

$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirmPassword = $_POST['confirm_password'];

if (
    empty($fullname) ||
    empty($email) ||
    empty($password) ||
    empty($confirmPassword)
) {
    die("All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email.");
}

if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}

$check = mysqli_prepare($conn, "SELECT id FROM admins WHERE email=?");
mysqli_stmt_bind_param($check, "s", $email);
mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {
    die("Email already exists.");
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$insert = mysqli_prepare($conn, "INSERT INTO admins(fullname,email,password) VALUES(?,?,?)");

mysqli_stmt_bind_param(
    $insert,
    "sss",
    $fullname,
    $email,
    $passwordHash
);

if (mysqli_stmt_execute($insert)) {
    $_SESSION['success'] = "Registration successful.";
    header("Location: ../auth/admin_login.php");
    exit();
} else {
    die("Registration failed.");
}
