<?php
require_once __DIR__ . '/../functions/student_auth.php';
start_student_session();

if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
    header("Location: ../student/profile.php");
    exit();
}

$message = "";

if (isset($_GET['error'])) {
    if ($_GET['error'] == "exists") {
        $message = "Email already registered.";
    } elseif ($_GET['error'] == "password") {
        $message = "Passwords do not match.";
    } elseif ($_GET['error'] == "empty") {
        $message = "Please fill all fields.";
    }
}

if (isset($_GET['success'])) {
    $message = "Registration successful. You can now login.";
} elseif (isset($_GET['from']) && $_GET['from'] === 'enrollment') {
    $message = "Application submitted. Create your account with the same email to access your profile.";
}

$alertIcon = isset($_GET['error']) ? 'error' : (isset($_GET['from']) ? 'info' : 'success');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AYYSAT Portal | Registration</title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/auth.css">
    <script src="../js/sweetalert2.all.min.js"></script>
</head>

<body>

    <div class="auth-page">

        <div class="auth-hero">
            <div class="hero-content">
                <span class="hero-badge">Join EYYSAT</span>
                <h2>Create your account and begin your journey</h2>
                <p>Register with the same streamlined experience as our student portal and start your next step with confidence.</p>
                <div class="hero-points">
                    <span>Simple setup</span>
                    <span>Secure account</span>
                    <span>Modern onboarding</span>
                </div>
            </div>
        </div>

        <div class="reg-container">

            <div class="reg-header">
                <a href="../webhome.php">
                    <img src="../img/eyysat.png" alt="AYYSAT Logo" class="portal-logo">
                </a>
                <h1>EYYSAT PORTAL</h1>
                <p>Create Your Account</p>
            </div>

            <div class="reg-body">

                <form action="../functions/register_process.php" method="POST">

                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="firstname" placeholder="Name" required>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastname" placeholder="Surname" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="student@eyysat.edu" required>
                    </div>

                    <div class="form-group">
                        <label>Create Password</label>
                        <input type="password" name="password" id="password" minlength="8" required>
                    </div>

                    <div class="form-group">
                        <label>Re-Enter Password</label>
                        <input type="password" name="confirm_password" id="confirmPassword" minlength="8" required>
                    </div>
                          
                    <div class="form-group checkbox-row">
                        <input type="checkbox" id="showPassword">
                        <label for="showPassword" class="checkbox-label">Show Password</label>
                    </div>

                    <button type="submit" class="reg-btn">
                        Complete Registration
                    </button>

                </form>

            </div>

            <div class="reg-footer">
                Already registered?
                <a href="login.php">
                    Sign In to Portal
                </a>
            </div>

        </div>

    </div>
    <script>
        <?php if ($message !== ''): ?>
            Swal.fire({
                icon: <?= json_encode($alertIcon) ?>,
                text: <?= json_encode($message) ?>,
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        const showPassword = document.getElementById('showPassword');
        const passwordFields = [
            document.getElementById('password'),
            document.getElementById('confirmPassword')
        ];

        showPassword.addEventListener('change', () => {
            passwordFields.forEach((field) => {
                field.type = showPassword.checked ? 'text' : 'password';
            });
        });
    </script>

</body>

</html>
