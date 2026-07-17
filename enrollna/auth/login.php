<?php

session_start();

if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] == "student") {

        header("Location: ../student/student_dashboard.php");
    }

    exit();
}

$message = "";

if (isset($_GET['error'])) {

    switch ($_GET['error']) {

        case "empty":
            $message = "Please enter your email and password.";
            break;

        case "invalid":
            $message = "Invalid email or password.";
            break;

        case "email":
            $message = "Please enter a valid email address.";
            break;
    }
}

if (isset($_GET['success'])) {

    if ($_GET['success'] == "registered") {

        $message = "Registration successful! You may now sign in.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AYYSAT Portal | Login</title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/login.css">

</head>

<body>

    <div class="portal-container">

        <div class="portal-header">

            <h1>AYYSAT PORTAL</h1>

            <p>Student & Staff Login</p>

        </div>

        <div class="portal-body">

            <?php if (!empty($message)) { ?>

                <div class="alert alert-info">

                    <?= htmlspecialchars($message); ?>

                </div>

            <?php } ?>

            <form action="../functions/login_process.php" method="POST">

                <div class="form-group">

                    <label>Email Address</label>

                    <input type="email" name="email" placeholder="student@ayysat.edu" required autocomplete="email">

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input type="password" name="password" placeholder="Password" required
                        autocomplete="current-password">

                </div>

                <div class="form-options">

                    <label>

                        <input type="checkbox" name="remember">

                        Remember Me

                    </label>

                    <a href="#">
                        Forgot Password?
                    </a>

                </div>

                <button type="submit" class="login-btn">

                    Sign In

                </button>

            </form>

        </div>

        <div class="portal-footer">

            Don't have an account?

            <a href="register.php">

                Create an Account

            </a>

        </div>

    </div>

</body>

</html>