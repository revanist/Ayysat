<?php
require_once __DIR__ . '/../functions/session_security.php';
start_secure_session();

$step = isset($_SESSION['password_reset_user_id']) ? 'set' : 'email';
$error = '';

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'email_not_found') {
        $error = 'No account was found with that email address.';
    } elseif ($_GET['error'] === 'password_mismatch') {
        $error = 'The new passwords do not match.';
    } elseif ($_GET['error'] === 'shortpassword') {
        $error = 'Password must be at least 8 characters long.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <a href="../webhome.php">
                    <img src="../img/eyysat.png" alt="AYYSAT Logo">
                </a>
                <h1>Eyysat</h1>
                <p>Reset Your Password</p>
            </div>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <?php if ($step === 'set') { ?>
                <form method="POST" action="../functions/forgot_password_process.php">
                    <input type="hidden" name="action" value="set_password">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" placeholder="Enter new password" minlength="8" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" placeholder="Re-enter new password" minlength="8" required>
                    </div>
                    <button type="submit" class="btn">Save New Password</button>
                </form>
            <?php } else { ?>
                <form method="POST" action="../functions/forgot_password_process.php">
                    <input type="hidden" name="action" value="verify_email">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="student@eyysat.edu" required>
                    </div>
                    <button type="submit" class="btn">Continue</button>
                </form>
            <?php } ?>

            <div class="link">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
