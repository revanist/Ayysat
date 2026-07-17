<?php

session_start();
require "../db/dbconn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Clean input
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['email']); // Email will be used as username
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Full name
    $fullname = $firstname . " " . $lastname;

    // Check empty fields
    if (
        empty($firstname) ||
        empty($lastname) ||
        empty($username) ||
        empty($password) ||
        empty($confirm_password)
    ) {
        header("Location: ../auth/register.php?error=empty");
        exit();
    }

    // Validate email
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../auth/register.php?error=email");
        exit();
    }

    // Check password
    if ($password !== $confirm_password) {
        header("Location: ../auth/register.php?error=password");
        exit();
    }

    // Minimum password length
    if (strlen($password) < 8) {
        header("Location: ../auth/register.php?error=shortpassword");
        exit();
    }

    // Check if username/email already exists
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check, "s", $username);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        mysqli_stmt_close($check);
        header("Location: ../auth/register.php?error=exists");
        exit();
    }

    mysqli_stmt_close($check);

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO users (fullname, username, password, role)
         VALUES (?, ?, ?, 'student')"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $fullname,
        $username,
        $hashedPassword
    );

    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        header("Location: ../auth/login.php?success=registered");
        exit();
    } else {

        mysqli_stmt_close($stmt);
        die("Registration failed: " . mysqli_error($conn));
    }
} else {

    header("Location: ../auth/register.php");
    exit();
}
