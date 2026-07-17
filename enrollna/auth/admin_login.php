<?php

session_start();
require "../db/dbconn.php";

if (isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin_dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, fullname, email, password
                FROM admins
                WHERE email=?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);

            if (
                password_verify($password, $admin['password'])
            ) {

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                $_SESSION['admin_email'] = $admin['email'];
                header("Location: ../admin/admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet"
        href="../css/style.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

</head>

<body>

    <div class="container">
        <div class="card">
            <div class="logo">
                <img src="../img/eyysat.png" alt="Logo">

                <h1>Eyysat</h1>
                <p>Administrator Login</p>
            </div>

            <?php if ($error != "") { ?>

                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <?php
            if (isset($_SESSION['success'])) { ?>

                <div class="alert alert-success">

                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="youremail@gmail.com" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>

                <button type="submit" name="login" class="btn">Login</button>
            </form>

            <div class="link">
                Don't have an account?
                <a href="admin_register.php">Register Here</a>
            </div>
        </div>

    </div>

</body>

</html>