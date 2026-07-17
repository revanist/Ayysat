<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "student") {

    header("Location: ../auth/login.php");
    exit();
}

include "../db/dbconn.php";


$user_id = $_SESSION['user_id'];


// Get student data

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


// Default status

$status = "Not Submitted";
$payment = "Pending";
$school_year = "N/A";
$semester = "N/A";


// Get enrollment

if ($student) {

    $enrollQuery = mysqli_query($conn, "

    SELECT *

    FROM enrollments

    WHERE student_id='{$student['id']}'

    ORDER BY id DESC

    LIMIT 1

    ");


    if (mysqli_num_rows($enrollQuery) > 0) {

        $enrollment = mysqli_fetch_assoc($enrollQuery);

        $status = $enrollment['STATUS'];
        $payment = $enrollment['payment_status'];
        $school_year = $enrollment['school_year'];
        $semester = $enrollment['sem'];
    }
}


?>


<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <title>Enrollment Status</title>


    <link rel="stylesheet" href="../css/admin_dashboard.css">


</head>


<body>


    <div class="dashboard-container">


        <!-- NAVBAR -->


        <div class="navbar">


            <div class="logo">

                <div class="logo-circle"></div>

                <h2>Eyysat</h2>

            </div>



            <div class="nav-menu">


                <a href="student_dashboard.php">

                    Dashboard

                </a>


                <a href="enrollment.php">

                    Enrollment

                </a>


                <a href="enrollment_status.php" class="active">

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

                <?php echo $_SESSION['fullname']; ?>

            </div>


        </div>





        <!-- HERO -->


        <div class="hero">


            <div class="welcome">


                <h1>

                    Enrollment Status

                </h1>


                <p>

                    Track your admission application progress

                </p>


                <h2>

                    <?php echo $status; ?>

                </h2>


                <span>

                    Current Application Status

                </span>


            </div>




            <div class="hero-stats">


                <div class="mini-stat">

                    <h3>

                        School Year

                    </h3>


                    <span>

                        <?php echo $school_year; ?>

                    </span>


                </div>



                <div class="mini-stat">


                    <h3>

                        Semester

                    </h3>


                    <span>

                        <?php echo $semester; ?>

                    </span>


                </div>



                <div class="mini-stat">


                    <h3>

                        Payment

                    </h3>


                    <span>

                        <?php echo $payment; ?>

                    </span>


                </div>


            </div>


        </div>






        <!-- CONTENT -->


        <div class="analytics-grid">



            <div class="card">


                <h3>

                    Student Information

                </h3>



                <p>

                    <strong>Name:</strong>

                    <?php echo $_SESSION['fullname']; ?>

                </p>



                <p>

                    <strong>Student Number:</strong>

                    <?php

                    echo $student ? $student['student_number'] : "Pending";

                    ?>

                </p>



                <p>

                    <strong>Course:</strong>

                    <?php

                    echo $student ? $student['course_name'] : "Not Selected";

                    ?>

                </p>


            </div>







            <div class="card">


                <h3>

                    Application Progress

                </h3>



                <div class="progress">


                    <div class="step active">

                        1. Account Created

                    </div>



                    <div class="step 
<?php echo ($status != "Not Submitted") ? 'active' : ''; ?>">

                        2. Application Submitted

                    </div>



                    <div class="step

<?php echo ($status == "Approved") ? 'active' : ''; ?>">

                        3. Approved

                    </div>



                    <div class="step

<?php echo ($status == "Approved" && $payment == "Paid") ? 'active' : ''; ?>">

                        4. Enrolled

                    </div>


                </div>


            </div>








            <div class="card">


                <h3>

                    Enrollment Details

                </h3>


                <p>

                    <strong>Status:</strong>

                    <span class="badge">

                        <?php echo $status; ?>

                    </span>

                </p>


                <p>

                    <strong>Payment:</strong>

                    <?php echo $payment; ?>

                </p>


                <p>

                    <strong>School Year:</strong>

                    <?php echo $school_year; ?>

                </p>


                <p>

                    <strong>Semester:</strong>

                    <?php echo $semester; ?>

                </p>


            </div>








            <div class="card">


                <h3>

                    Important Notices

                </h3>


                <ul>


                    <li>

                        Wait for admin approval of your application.

                    </li>


                    <li>

                        Complete your payment requirements.

                    </li>


                    <li>

                        Check your dashboard regularly for updates.

                    </li>


                </ul>


            </div>





        </div>


    </div>


</body>

</html>