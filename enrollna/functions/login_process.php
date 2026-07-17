<?php

session_start();

require "../db/dbconn.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $email = trim($_POST['email']);
    $password = $_POST['password'];


    // Check empty fields

    if (empty($email) || empty($password)) {

        header("Location: ../auth/login.php?error=empty");
        exit();
    }



    // Find user

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, fullname, username, password, role
         FROM users
         WHERE username = ?"
    );


    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $email
    );


    mysqli_stmt_execute($stmt);


    $result = mysqli_stmt_get_result($stmt);



    if (mysqli_num_rows($result) > 0) {


        $user = mysqli_fetch_assoc($result);



        // Check password

        if (password_verify($password, $user['password'])) {


            // Create session

            $_SESSION['user_id'] = $user['id'];

            $_SESSION['fullname'] = $user['fullname'];

            $_SESSION['username'] = $user['username'];

            $_SESSION['role'] = $user['role'];



            // Redirect based on role

            if ($user['role'] == "student") {


                header("Location: ../student/student_dashboard.php");
            }


            exit();
        } else {


            header("Location: ../auth/login.php?error=invalid");

            exit();
        }
    } else {


        header("Location: ../auth/login.php?error=invalid");

        exit();
    }
} else {


    header("Location: ../auth/login.php");

    exit();
}
