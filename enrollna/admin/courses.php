<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit();
}

include "../db/dbconn.php";

$selected_course = isset($_GET['course']) ? intval($_GET['course']) : 0;

$coursesQuery = mysqli_query($conn, "
SELECT
    courses.id,
    courses.course_code,
    courses.course_name,
    COUNT(sections.id) total_sections
FROM courses
LEFT JOIN sections
ON courses.id=sections.course_id
GROUP BY courses.id
ORDER BY courses.course_code
");

$totalCourses = mysqli_num_rows($coursesQuery);

$totalSections = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) total
FROM sections
"))['total'];

$totalSubjects = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) total
FROM subjects
"))['total'];

mysqli_data_seek($coursesQuery, 0);
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>Courses</title>

    <link rel="stylesheet" href="../css/admin_dashboard.css">

</head>

<body>

    <div class="dashboard-container">

        <?php include "navbar_layout.php"; ?>

        <!-- HERO -->

        <div class="hero">

            <div class="welcome">

                <h1>Courses</h1>

                <p>Manage academic programs, sections and subjects.</p>

            </div>

            <div class="hero-stats">

                <div class="mini-stat">
                    <h3>Courses</h3>
                    <span><?php echo $totalCourses; ?></span>
                </div>

                <div class="mini-stat">
                    <h3>Sections</h3>
                    <span><?php echo $totalSections; ?></span>
                </div>

                <div class="mini-stat">
                    <h3>Subjects</h3>
                    <span><?php echo $totalSubjects; ?></span>
                </div>

            </div>

        </div>

        <div class="analytics-grid">

            <!-- COURSES -->

            <div class="card">

                <h3>Available Courses</h3>

                <div class="course-grid">

                    <?php while ($course = mysqli_fetch_assoc($coursesQuery)) { ?>

                        <a href="courses.php?course=<?php echo $course['id']; ?>" class="course-card">

                            <h2><?php echo $course['course_code']; ?></h2>

                            <p><?php echo $course['course_name']; ?></p>

                            <span><?php echo $course['total_sections']; ?> Sections</span>

                        </a>

                    <?php } ?>

                </div>

            </div>

            <?php
            if ($selected_course > 0) {

                $courseInfo = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT *
FROM courses
WHERE id='$selected_course'
"));
            ?>

                <div class="card">

                    <h2>
                        <?php echo $courseInfo['course_code']; ?>
                        -
                        <?php echo $courseInfo['course_name']; ?>
                    </h2>

                    <hr><br>

                    <h3>Sections</h3>

                    <div class="course-grid">

                        <?php

                        $sections = mysqli_query($conn, "
SELECT *
FROM sections
WHERE course_id='$selected_course'
ORDER BY section_name
");

                        while ($section = mysqli_fetch_assoc($sections)) {
                        ?>

                            <div class="course-card">

                                <h3><?php echo $section['section_name']; ?></h3>

                                <p>Maximum Slots</p>

                                <h2><?php echo $section['max_slots']; ?></h2>

                            </div>

                        <?php } ?>

                    </div>

                    <br><br>

                    <h3>Subjects</h3>

                    <table class="modern-table">

                        <tr>

                            <th>Code</th>
                            <th>Subject</th>
                            <th>Units</th>
                            <th>Year</th>
                            <th>Semester</th>

                        </tr>

                        <?php

                        $subjects = mysqli_query($conn, "
SELECT *
FROM subjects
WHERE course_id='$selected_course'
ORDER BY year_level,sem
");

                        while ($subject = mysqli_fetch_assoc($subjects)) {
                        ?>

                            <tr>

                                <td><?php echo $subject['subject_code']; ?></td>

                                <td><?php echo $subject['subject_name']; ?></td>

                                <td><?php echo $subject['units']; ?></td>

                                <td><?php echo $subject['year_level']; ?></td>

                                <td><?php echo $subject['sem']; ?></td>

                            </tr>

                        <?php } ?>

                    </table>

                </div>

            <?php } ?>

        </div>

    </div>

</body>

</html>