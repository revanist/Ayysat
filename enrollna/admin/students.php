<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin_login.php");
    exit();
}

include "../db/dbconn.php";
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$query = mysqli_query($conn, "
        SELECT * FROM students
        WHERE student_number LIKE '%$search%'
        OR last_name LIKE '%$search%'
        OR email LIKE '%$search%'
    ");

$totalStudents = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) total
    FROM students
    "))['total'];

$query = mysqli_query($conn, "
    SELECT
        students.id,
        students.student_number,
        students.first_name,
        students.last_name,
        students.email,
        students.year_level,
        courses.course_code,
        sections.section_name

    FROM students

    LEFT JOIN enrollments
    ON students.id = enrollments.student_id

    LEFT JOIN courses
    ON students.course_id = courses.id

    LEFT JOIN sections
    ON students.section_id = sections.id    

    ORDER BY students.id DESC
    ");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>

<body>

    <div class="dashboard-container">

        <?php include "navbar_layout.php"; ?>

        <div class="page-header">
            <h1>Students</h1>
            <p>Manage enrolled students</p>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <h3>Total Students</h3>
                <span><?php echo $totalStudents; ?></span>
            </div>

            <div class="stat-card">
                <h3>Active Enrollments</h3>
                <span><?php echo mysqli_num_rows($query); ?></span>
            </div>
        </div>
        <div class="card">
            <div class="table-header">
                <h2>Student List</h2>
                <input type="text" name="search" id="searchInput" placeholder="Search student..." class="search-box">
            </div>

            <table class="modern-table" id="studentTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student No.</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Email</th>
                        <th>Section</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>

                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['student_number']; ?></td>
                            <td>
                                <?php
                                echo $row['first_name'] . ' ' . $row['last_name'];
                                ?>
                            </td>
                            <td><?php echo $row['course_code']; ?></td>
                            <td><?php echo $row['year_level']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['section_name']; ?></td>
                            <td>

                                <a href="edit_student.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-warning">
                                    Edit
                                </a>


                                <a href="../controller/delete_student.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this student?');">
                                    Delete
                                </a>

                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <script>
        document.getElementById('searchInput')
            .addEventListener('keyup', function() {

                let value = this.value.toLowerCase();
                let rows = document.querySelectorAll('#studentTable tbody tr');
                rows.forEach(row => {
                    row.style.display =
                        row.innerText.toLowerCase().includes(value) ?
                        '' :
                        'none';
                });
            });
    </script>

</body>

</html>