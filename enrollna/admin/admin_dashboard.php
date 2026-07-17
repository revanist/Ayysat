<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin_login.php");
    exit();
}

include "../db/dbconn.php";

$students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM students"))['total'];
$courses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM courses"))['total'];
$subjects = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM subjects"))['total'];
$enrollments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM enrollments"))['total'];

$pendingPayments = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) total FROM enrollments WHERE payment_status='pending'
    "))['total'] ?? 0;

$approved = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) total FROM enrollments WHERE STATUS='approved'
    "))['total'] ?? 0;

$paid = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) total FROM enrollments WHERE payment_status='paid'
    "))['total'] ?? 0;

$sections = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) total FROM sections
    "))['total'] ?? 0;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="dashboard-container">

        <div class="navbar">
            <div class="logo">
                <div class="logo-circle"></div>
                <h2>Eyysat</h2>
            </div>

            <div class="nav-menu">
                <a href="admin_dashboard.php" class="active">Dashboard</a>
                <a href="students.php">Students</a>
                <a href="courses.php">Courses</a>
                <a href="enrollments.php">Enrollments</a>
                <a href="cashier.php">Cashier</a>
                <a href="reports.php">Reports</a>
                <a href="../auth/admin_logout.php">Logout</a>
            </div>

            <div class="profile">
                Admin
            </div>
        </div>

        <div class="hero">
            <div class="welcome">
                <h1>Welcome back,
                    <?php echo $_SESSION['admin_name']; ?>
                </h1>
                <p>Enrollment Management System Dashboard</p>
                <h2>
                    <?php echo $students; ?>
                </h2>
                <span>Total Students</span>
            </div>

            <div class="hero-stats">
                <div class="mini-stat">
                    <h3>Courses</h3>
                    <span>
                        <?php echo $courses; ?>
                    </span>
                </div>
                <div class="mini-stat">
                    <h3>Subjects</h3>
                    <span>
                        <?php echo $subjects; ?>
                    </span>
                </div>
                <div class="mini-stat">
                    <h3>Enrollments</h3>
                    <span>
                        <?php echo $enrollments; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="analytics-grid">

            <div class="card chart-card">
                <h3>Analytics</h3>
                <canvas id="enrollmentChart"></canvas>
            </div>

            <div class="card activity-card">
                <h3>Quick Stats</h3>
                <div class="activity-grid">
                    <div>
                        <strong>
                            <?php echo $pendingPayments; ?>
                        </strong>
                        <small>Pending</small>
                    </div>
                    <div>
                        <strong>
                            <?php echo $approved; ?>
                        </strong>
                        <small>Approved</small>
                    </div>
                    <div>
                        <strong>
                            <?php echo $paid; ?>
                        </strong>
                        <small>Paid</small>
                    </div>
                    <div>
                        <strong>
                            <?php echo $sections; ?>
                        </strong>
                        <small>Sections</small>
                    </div>
                </div>
            </div>

            <div class="card recent-card">
                <h3>Recent Enrollments</h3>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Payment</th>
                        <th>Status</th>
                    </tr>

                    <?php
                    $recentQuery = mysqli_query($conn, "
                        SELECT
                        students.student_number,
                        students.first_name,
                        students.last_name,
                        courses.course_code,
                        enrollments.payment_status,
                        enrollments.STATUS
                        FROM enrollments
                        INNER JOIN students ON enrollments.student_id = students.id
                        INNER JOIN courses ON students.course_id = courses.id
                        ORDER BY enrollments.id DESC
                        LIMIT 10
                        ");

                    while ($row = mysqli_fetch_assoc($recentQuery)) {
                    ?>
                        <tr>
                            <td>
                                <?php echo $row['student_number']; ?>
                            </td>
                            <td>
                                <?php echo $row['first_name'] . ' ' . $row['last_name']; ?>
                            </td>
                            <td>
                                <?php echo $row['course_code']; ?>
                            </td>
                            <td>
                                <?php echo $row['payment_status']; ?>
                            </td>
                            <td>
                                <?php echo $row['STATUS']; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

        </div>
    </div>
    </div>

    <script>
        const ctx = document.getElementById('enrollmentChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Courses', 'Subjects', 'Enrollments'],
                datasets: [{
                    data: [<?php echo $students; ?>, <?php echo $courses; ?>, <?php echo $subjects; ?>, <?php echo $enrollments; ?>],
                    backgroundColor: ['#d8ff61', '#7c3aed', '#8b5cf6', '#c084fc']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>