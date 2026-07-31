<?php
require_once __DIR__ . '/student_auth.php';
start_student_session();
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

        $user_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Link a guest application only after its applicant creates the
        // account using the same email address.
        $link_application = mysqli_prepare($conn, 'UPDATE students SET user_id = ? WHERE user_id IS NULL AND email = ?');
        mysqli_stmt_bind_param($link_application, 'is', $user_id, $username);
        mysqli_stmt_execute($link_application);
        $affected = mysqli_stmt_affected_rows($link_application);
        mysqli_stmt_close($link_application);

        // If no pre-existing application was linked, create an initial student record
        if ($affected === 0) {
            $stub_student = mysqli_prepare($conn, '
                INSERT INTO students (user_id, first_name, last_name, email, payment_status, enrollment_status, remaining_balance)
                VALUES (?, ?, ?, ?, \'Pending\', \'Pending\', 15000.00)
            ');
            mysqli_stmt_bind_param($stub_student, 'isss', $user_id, $firstname, $lastname, $username);
            mysqli_stmt_execute($stub_student);
            mysqli_stmt_close($stub_student);
        }

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
