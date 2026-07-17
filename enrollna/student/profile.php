<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "student") {
    header("Location: ../auth/login.php");
    exit();
}

include "../db/dbconn.php";

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
SELECT
    students.*,
    courses.course_name,
    courses.course_code
FROM students
LEFT JOIN courses
ON students.course_id = courses.id
WHERE students.user_id='$user_id'
");

$student = mysqli_fetch_assoc($query);

if (!$student) {
    die("Student profile not found.");
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Student Profile</title>

    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>

<body>

    <div class="dashboard-container">

        <!-- NAVBAR -->

        <div class="navbar">

            <div class="logo">

                <div class="logo-circle">
                    <img src="../images/logo.png" alt="">
                </div>

                <h2>Eyysat</h2>

            </div>

            <div class="nav-menu">

                <a href="student_dashboard.php">Dashboard</a>
                <a href="enrollment.php">Enrollment</a>
                <a href="enrollment_status.php">Enrollment Status</a>
                <a href="profile.php" class="active">Profile</a>
                <a href="../auth/logout.php">Logout</a>

            </div>

            <div class="profile">
                <?php echo $_SESSION['fullname']; ?>
            </div>

        </div>

        <!-- HERO -->

        <div class="hero">

            <div class="welcome">

                <h1>Student Profile</h1>

                <p>Manage your personal information.</p>

                <h2><?php echo $student['student_number']; ?></h2>

                <span>Student Number</span>

            </div>

            <div class="hero-stats">

                <div class="mini-stat">
                    <h3>Course</h3>
                    <span><?php echo $student['course_code']; ?></span>
                </div>

                <div class="mini-stat">
                    <h3>Year</h3>
                    <span><?php echo $student['year_level']; ?></span>
                </div>

                <div class="mini-stat">
                    <h3>Status</h3>
                    <span>Active</span>
                </div>

            </div>

        </div>

        <!-- PROFILE CARD -->

        <div class="analytics-grid">

            <div class="card">

                <h3>Personal Information</h3>

                <form action="../functions/student_update_profile.php" method="POST">

                    <input type="hidden"
                        name="id"
                        value="<?php echo $student['id']; ?>">

                    <label>First Name</label>

                    <input type="text"
                        name="first_name"
                        value="<?php echo $student['first_name']; ?>">

                    <label>Middle Name</label>

                    <input type="text"
                        name="middle_name"
                        value="<?php echo $student['middle_name']; ?>">

                    <label>Last Name</label>

                    <input type="text"
                        name="last_name"
                        value="<?php echo $student['last_name']; ?>">

                    <label>Email</label>

                    <input type="email"
                        name="email"
                        value="<?php echo $student['email']; ?>">

                    <label>Contact Number</label>

                    <input type="text"
                        name="contact"
                        value="<?php echo $student['contact']; ?>">

                    <label>Address</label>

                    <textarea
                        name="address"
                        rows="4"><?php echo $student['address']; ?></textarea>

                    <br><br>

                    <button class="btn">
                        Save Changes
                    </button>

                </form>

            </div>

            <div class="card">

                <h3>Academic Information</h3>

                <p><strong>Student No:</strong>
                    <?php echo $student['student_number']; ?>
                </p>

                <p><strong>Course:</strong>
                    <?php echo $student['course_name']; ?>
                </p>

                <p><strong>Course Code:</strong>
                    <?php echo $student['course_code']; ?>
                </p>

                <p><strong>Year Level:</strong>
                    <?php echo $student['year_level']; ?>
                </p>

                <p><strong>Birthdate:</strong>
                    <?php echo $student['birthdate']; ?>
                </p>

                <p><strong>Sex:</strong>
                    <?php echo $student['sex']; ?>
                </p>

            </div>

        </div>

    </div>

</body>

</html>