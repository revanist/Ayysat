<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit();
}

include "../db/dbconn.php";

$query = mysqli_query($conn, "
    SELECT
        enrollments.id,
        students.student_number,
        students.first_name,
        students.last_name,
        courses.course_code,
        enrollments.school_year,
        enrollments.sem,
        enrollments.STATUS,
        enrollments.created
    FROM enrollments
    INNER JOIN students
        ON enrollments.student_id = students.id
    LEFT JOIN courses
        ON students.course_id = courses.id
    ORDER BY enrollments.created DESC
");

$pending = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) total
FROM enrollments
WHERE STATUS='pending'
"))['total'];

$approved = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) total
FROM enrollments
WHERE STATUS='approved'
"))['total'];

$rejected = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) total
FROM enrollments
WHERE STATUS='rejected'
"))['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Enrollment Management</title>

    <link rel="stylesheet" href="../css/admin_dashboard.css">

</head>

<body>

    <div class="dashboard-container">

        <?php include "navbar_layout.php"; ?>

        <div class="hero">

            <div class="welcome">

                <h1>Enrollment Management</h1>

                <p>Review and approve student enrollment applications.</p>

            </div>

        </div>

        <div class="hero-stats">

            <div class="mini-stat">
                <h3>Pending</h3>
                <span><?php echo $pending; ?></span>
            </div>

            <div class="mini-stat">
                <h3>Approved</h3>
                <span><?php echo $approved; ?></span>
            </div>

            <div class="mini-stat">
                <h3>Rejected</h3>
                <span><?php echo $rejected; ?></span>
            </div>

        </div>

        <div class="analytics-grid">

            <div class="card recent-card">

                <h3>Enrollment Records</h3>

                <table>

                    <tr>

                        <th>ID</th>
                        <th>Student No.</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>School Year</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>

                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>

                        <tr>

                            <td><?php echo $row['id']; ?></td>

                            <td><?php echo $row['student_number']; ?></td>

                            <td>

                                <?php
                                echo $row['first_name'] . " " . $row['last_name'];
                                ?>

                            </td>

                            <td><?php echo $row['course_code']; ?></td>

                            <td><?php echo $row['school_year']; ?></td>

                            <td><?php echo $row['sem']; ?></td>

                            <td>

                                <?php

                                $status = strtolower($row['STATUS']);

                                if ($status == "approved") {

                                    echo "<span class='status-approved'>Approved</span>";
                                } elseif ($status == "pending") {

                                    echo "<span class='status-pending'>Pending</span>";
                                } else {

                                    echo "<span class='status-rejected'>Rejected</span>";
                                }

                                ?>

                            </td>

                            <td>

                                <?php

                                echo date("M d, Y", strtotime($row['created']));

                                ?>

                            </td>

                            <td>

                                <div class="action-buttons">

                                    <a href="student_info.php?id=<?php echo $row['id']; ?>" class="btn-view">
                                        View
                                    </a>

                                    <?php if ($status == "pending") { ?>

                                        <a href="../functions/update_enrollment.php?id=<?php echo $row['id']; ?>&status=approved"
                                            class="btn-edit"
                                            onclick="return confirm('Approve this enrollment?');">
                                            Approve
                                        </a>

                                        <a href="../functions/update_enrollment.php?id=<?php echo $row['id']; ?>&status=rejected"
                                            class="btn-delete"
                                            onclick="return confirm('Reject this enrollment?');">
                                            Reject
                                        </a>

                                    <?php } else { ?>

                                        <span class="completed-text">Completed</span>

                                    <?php } ?>

                                </div>

                            </td>
                        </tr>

                    <?php } ?>

                </table>

            </div>

        </div>

    </div>

</body>

</html>