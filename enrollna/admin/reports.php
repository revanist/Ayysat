<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin_login.php");
    exit();
}

include "../db/dbconn.php";

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM students")
)['total'];

$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses")
)['total'];

$totalSubjects = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM subjects")
)['total'];

$totalSections = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM sections")
)['total'];

$totalEnrollments = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM enrollments")
)['total'];

$approved = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM enrollments WHERE STATUS='approved'")
)['total'];

$pending = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM enrollments WHERE STATUS='pending'")
)['total'];

$rejected = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM enrollments WHERE STATUS='rejected'")
)['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>

<body>
    <div class="dashboard-container">

        <?php include "navbar_layout.php"; ?>

        <div class="page-header">
            <h1>Reports</h1>
            <p>System statistics and enrollment analytics</p>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <h3>Students</h3>
                <span><?php echo $totalStudents; ?></span>
            </div>

            <div class="stat-card">
                <h3>Courses</h3>
                <span><?php echo $totalCourses; ?></span>
            </div>

            <div class="stat-card">
                <h3>Subjects</h3>
                <span><?php echo $totalSubjects; ?></span>
            </div>

            <div class="stat-card">
                <h3>Sections</h3>
                <span><?php echo $totalSections; ?></span>
            </div>

            <div class="stat-card">
                <h3>Enrollments</h3>
                <span><?php echo $totalEnrollments; ?></span>
            </div>

        </div>

        <div class="analytics-grid">
            <div class="card">
                <h3>Enrollment Status</h3>
                <div class="activity-grid">

                    <div>
                        <strong><?php echo $approved; ?></strong>
                        <small>Approved</small>
                    </div>

                    <div>
                        <strong><?php echo $pending; ?></strong>
                        <small>Pending</small>
                    </div>

                    <div>
                        <strong><?php echo $rejected; ?></strong>
                        <small>Rejected</small>
                    </div>

                    <div>
                        <strong><?php echo $totalEnrollments; ?></strong>
                        <small>Total</small>
                    </div>

                </div>

            </div>

            <div class="card">

                <h3>System Summary</h3>
                <p style="line-height:1.8;color:#bdbdbd;">

                    This report provides an overview of the Enrollment
                    Management System including students, courses,
                    subjects, sections and enrollment statistics.
                </p>
                <br>
                <p style="color:#d8ff61;">
                    Current system activity and enrollment status
                    can be monitored from this page.
                </p>
            </div>
        </div>

        <div class="card" style="margin-top:25px;">

            <h3>Detailed Statistics</h3>

            <table class="modern-table">

                <tr>
                    <th>Category</th>
                    <th>Total</th>
                </tr>

                <tr>
                    <td>Total Students</td>
                    <td><?php echo $totalStudents; ?></td>
                </tr>

                <tr>
                    <td>Total Courses</td>
                    <td><?php echo $totalCourses; ?></td>
                </tr>

                <tr>
                    <td>Total Subjects</td>
                    <td><?php echo $totalSubjects; ?></td>
                </tr>

                <tr>
                    <td>Total Sections</td>
                    <td><?php echo $totalSections; ?></td>
                </tr>

                <tr>
                    <td>Total Enrollments</td>
                    <td><?php echo $totalEnrollments; ?></td>
                </tr>

                <tr>
                    <td>Approved</td>
                    <td class="status-approved">
                        <?php echo $approved; ?>
                    </td>
                </tr>

                <tr>
                    <td>Pending</td>
                    <td class="status-pending">
                        <?php echo $pending; ?>
                    </td>
                </tr>

                <tr>
                    <td>Rejected</td>
                    <td class="status-rejected">
                        <?php echo $rejected; ?>
                    </td>
                </tr>

            </table>

        </div>

    </div>
</body>

</html>