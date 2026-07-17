<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AYYSAT Portal | Registration</title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/register.css">
</head>

<body>

    <div class="reg-container">

        <div class="reg-header">
            <h1>STUDENT ENROLLMENT</h1>
            <p>Create Your AYYSAT Account</p>
        </div>


        <div class="reg-body">

            <?php if ($message != ""): ?>
                <p class="alert alert-info">
                    <?php echo $message; ?>
                </p>
            <?php endif; ?>


            <form action="../functions/register_process.php" method="POST">


                <div class="form-group">
                    <label>First Name</label>
                    <input
                        type="text"
                        name="firstname"
                        placeholder="Name"
                        required>
                </div>


                <div class="form-group">
                    <label>Last Name</label>
                    <input
                        type="text"
                        name="lastname"
                        placeholder="Surname"
                        required>
                </div>


                <div class="form-group">
                    <label>Email Address</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="student@ayysat.edu"
                        required>
                </div>


                <div class="form-group">
                    <label>Create Password</label>
                    <input
                        type="password"
                        name="password"
                        minlength="8"
                        required>
                </div>


                <div class="form-group">
                    <label>Re-Enter Password</label>
                    <input
                        type="password"
                        name="confirm_password"
                        minlength="8"
                        required>
                </div>


                <div class="terms-text">

                    <input
                        type="checkbox"
                        name="terms"
                        required>

                    <label>
                        I certify that all information provided is accurate and I agree to follow the university code of conduct.
                    </label>

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


</body>

</html>