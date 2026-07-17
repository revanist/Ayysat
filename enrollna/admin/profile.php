<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin_login.php");
    exit();
}

include "../db/dbconn.php";

$admin_id = $_SESSION['admin_id'];

$query = mysqli_query($conn, "
        SELECT *
        FROM admins
        WHERE id = '$admin_id'
    ");

$admin = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div class="sidebar">

        <div class="brand">🎓 Enrollment</div>

        <a href="profile.php" class="active">Profile</a>
        <hr>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="students.php">Students</a>
        <a href="courses.php">Courses</a>
        <a href="enrollments.php">Enrollments</a>
        <a href="reports.php">Reports</a>
        <a href="../auth/admin_logout.phplogout.php">Logout</a>

    </div>

    <div class="main">

        <div class="topbar">
            <h2>My Profile</h2>
        </div>

        <div class="content-card">

            <h3>Admin Information</h3>

            <p>
                <strong>Name:</strong>
                <?php echo $admin['name']; ?>
            </p>

            <p>
                <strong>Username:</strong>
                <?php echo $admin['username']; ?>
            </p>

            <p>
                <strong>ID:</strong>
                <?php echo $admin['id']; ?>
            </p>

        </div>

    </div>

</body>

</html>