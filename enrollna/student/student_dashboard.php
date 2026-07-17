<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "student") {

    header("Location: ../auth/login.php");
    exit();
}

include "../db/dbconn.php";


$user_id = $_SESSION['user_id'];


// Get student information

$studentQuery = mysqli_query($conn, "
    SELECT 
        students.*,
        courses.course_name,
        courses.course_code
    FROM students
    LEFT JOIN courses 
    ON students.course_id = courses.id
    WHERE students.user_id='$user_id'
");


$student = mysqli_fetch_assoc($studentQuery);


// Enrollment status

$status = "Not Submitted";
$payment = "Pending";


if ($student) {

    $enrollmentQuery = mysqli_query($conn, "
        SELECT status,payment_status
        FROM enrollments
        WHERE student_id='{$student['id']}'
        ORDER BY id DESC
        LIMIT 1
    ");

    if (mysqli_num_rows($enrollmentQuery) > 0) {

        $enrollment = mysqli_fetch_assoc($enrollmentQuery);

        $status = $enrollment['status'];
        $payment = $enrollment['payment_status'];
    }
}


// count announcements example

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard</title>


    <link rel="stylesheet" href="../css/admin_dashboard.css">


</head>

<body>
    <div class="dashboard-container">
        <!-- NAVBAR -->

        <!-- NAVBAR -->

        <div class="navbar">

            <div class="logo">

                <div class="logo-circle">
                    <img src="../images/logo.png" alt="Logo">
                </div>

                <h2>Eyysat</h2>

            </div>
            <div class="nav-menu">

                <a href="../student/student_dashboard.php" class="active">
                    Dashboard
                </a>

                <a href="enrollment.php">
                    Enrollment
                </a>

                <a href="enrollment_status.php">
                    Enrollment Status
                </a>

                <a href="profile.php">
                    Profile
                </a>

                <a href="../auth/logout.php">
                    Logout
                </a>

            </div>
            <div class="profile">

                <?php echo htmlspecialchars($_SESSION['fullname']); ?>

            </div>
        </div>
        <div class="hero">
            <div class="welcome">
                <h1>
                    Welcome back,
                    <?php echo $_SESSION['fullname']; ?>
                </h1>
                <p>
                    AYYSAT Student Enrollment Portal
                </p>
                <h2>

                    <?php

                    echo $student ? $student['student_number'] : "Pending";

                    ?>

                </h2>
                <span>
                    Student Number
                </span>
            </div>
            <div class="hero-stats">


                <div class="mini-stat">

                    <h3>Course</h3>

                    <span>

                        <?php

                        echo $student ? $student['course_code'] : "N/A";

                        ?>

                    </span>

                </div>
                <div class="mini-stat">

                    <h3>Enrollment</h3>

                    <span>

                        <?php echo $status; ?>

                    </span>

                </div>
                <div class="mini-stat">

                    <h3>Payment</h3>

                    <span>

                        <?php echo $payment; ?>

                    </span>

                </div>
            </div>
        </div>
        <div class="analytics-grid">
            <div class="card">


                <h3>
                    Enrollment Application
                </h3>


                <p>
                    Complete your college enrollment application.
                </p>


                <a href="enrollment.php" class="btn">

                    Enroll Now

                </a>
            </div>
            <div class="card">


                <h3>
                    Student Information
                </h3>

                <p>
                    <strong>Name:</strong>

                    <?php echo $_SESSION['fullname']; ?>

                </p>
                <p>

                    <strong>Course:</strong>

                    <?php

                    echo $student ? $student['course_name'] : "Not Selected";

                    ?>

                </p>
                <p>

                    <strong>Status:</strong>

                    <span>

                        <?php echo $status; ?>

                    </span>

                </p>


            </div>
            <div class="card">
                <h3>
                    Enrollment Progress
                </h3>
                <div class="progress">


                    <div class="step active">

                        1. Account Created

                    </div>
                    <div class="step">

                        2. Application Submitted

                    </div>
                    <div class="step">

                        3. Approved

                    </div>
                    <div class="step">

                        4. Enrolled

                    </div>
                </div>
            </div>
            <div class="card recent-card">


                <h3>
                    Announcements
                </h3>


                <ul>

                    <li>
                        Enrollment for SY 2026-2027 is now open.
                    </li>


                    <li>
                        Complete your profile before enrolling.
                    </li>


                    <li>
                        Prepare your admission requirements.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>