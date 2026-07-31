<?php
require_once __DIR__ . '/../functions/student_auth.php';
start_student_session();

if (!empty($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? '') === 'student') {
        header('Location: ../student/profile.php');
    }
    exit();
}

$errorMessages = [
    'empty' => 'Please enter your email and password.',
    'invalid' => 'Invalid email or password.',
    'email' => 'Please enter a valid email address.',
    'invalid_email' => 'Please enter a valid email address.',
    'email_not_found' => 'No account was found with that email address.',
];
$successMessages = [
    'registered' => 'Registration successful! You may now sign in.',
    'reset' => 'Please set your new password below.',
    'password_updated' => 'Your password has been updated successfully. Please sign in with your new password.',
];

$message = $errorMessages[$_GET['error'] ?? '']
    ?? $successMessages[$_GET['success'] ?? '']
    ?? '';
$alertIcon = isset($_GET['error']) ? 'error' : 'success';
$temporaryPassword = $_SESSION['temp_password'] ?? '';
unset($_SESSION['temp_password']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>EYYSAT Portal | Login</title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/auth.css">
    <script src="../js/sweetalert2.all.min.js"></script>

</head>

<body>

    <div class="auth-page">

        <div class="auth-hero">
            <div class="hero-content">
                <span class="hero-badge">Admission Open 2026-27</span>
                <h2>Welcome back to EYYSAT</h2>
                <p>Access your student and staff portal with the same thoughtful experience as our academy website.</p>
                <div class="hero-points">
                    <span>Fast sign in</span>
                    <span>Secure access</span>
                    <span>Modern experience</span>
                </div>
            </div>
        </div>

        <div class="portal-container">

            <div class="portal-header">
                <a href="../webhome.php">
                    <img src="../img/eyysat.png" alt="EYYSAT Logo" class="portal-logo">
                </a>
                <h1>EYYSAT PORTAL</h1>

                <p>Student Login</p>

            </div>

            <div class="portal-body">

                <?php if ($temporaryPassword !== '') { ?>
                    <div class="alert alert-warning">
                        Temporary password: <strong><?= htmlspecialchars($temporaryPassword); ?></strong>
                    </div>
                <?php } ?>

                <form action="../functions/login_process.php" method="POST" class="login-form">

                    <div class="form-group">

                        <label>Email Address</label>

                        <input type="email" name="email" placeholder="student@eyysat.edu" required autocomplete="email">

                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-field">
                            <input type="password" name="password" id="loginPassword" placeholder="Password" required
                                autocomplete="current-password">
                            <button type="button" class="password-toggle" id="passwordToggle"
                                aria-label="Show password" aria-controls="loginPassword" aria-pressed="false">
                                <svg class="icon-show" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path><circle cx="12" cy="12" r="2.8"></circle></svg>
                                <svg class="icon-hide" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A10.9 10.9 0 0 1 12 6c6.5 0 10 6 10 6a18 18 0 0 1-3.1 3.6M6.2 6.2A18 18 0 0 0 2 12s3.5 6 10 6c1.3 0 2.5-.2 3.5-.7M9.9 9.9a3 3 0 0 0 4.2 4.2"></path></svg>
                            </button>
                        </div>
                    </div>
                    

                    <div class="form-options">

                        <label>

                            <input type="checkbox" name="remember">

                            Remember Me

                        </label>

                        <a href="forgot_password.php">
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

    </div>

    <script>
        <?php if ($message !== ''): ?>
            Swal.fire({
                icon: <?= json_encode($alertIcon) ?>,
                text: <?= json_encode($message) ?>,
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        const passwordField = document.getElementById('loginPassword');
        const passwordToggle = document.getElementById('passwordToggle');

        passwordToggle?.addEventListener('click', () => {
            const isVisible = passwordField.type === 'text';
            passwordField.type = isVisible ? 'password' : 'text';
            passwordToggle.setAttribute('aria-pressed', String(!isVisible));
            passwordToggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
            passwordField.focus();
        });
    </script>
</body>

</html>
