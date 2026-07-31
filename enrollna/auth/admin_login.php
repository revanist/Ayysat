<?php
require_once __DIR__ . '/../functions/session_security.php';
start_secure_session();

require "../db/dbconn.php";

if (isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin_dashboard.php");
    exit();
}

$error = "";
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

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

                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                $_SESSION['admin_email'] = $admin['email'];
                echo '<script>window.location.replace("../admin/admin_dashboard.php");</script>';
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
    <link rel="stylesheet" href="../css/auth.css">
    <script src="../js/sweetalert2.all.min.js"></script>

</head>

<body>

    <div class="container">
        <div class="card">
            <div class="logo">
                <a href="../webhome.php">
                    <img src="../img/eyysat.png" alt="AYYSAT Logo">
                </a>
                <h1>Eyysat</h1>
                <p>Administrator Login</p>
            </div>

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

    <script>
        <?php if ($error !== ''): ?>
            Swal.fire({ icon: 'error', text: <?= json_encode($error) ?>, confirmButtonText: 'Try Again' });
        <?php elseif ($success !== ''): ?>
            Swal.fire({ icon: 'success', text: <?= json_encode($success) ?>, confirmButtonText: 'OK' });
        <?php endif; ?>
    </script>

</body>

</html>
