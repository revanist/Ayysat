<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit();
}

include "../db/dbconn.php";

/* Pending Payments */

$query = mysqli_query($conn, "
SELECT
    enrollments.id,
    students.student_number,
    students.first_name,
    students.last_name,
    courses.course_code,
    enrollments.payment_status,
    enrollments.STATUS,
    enrollments.created

FROM enrollments

INNER JOIN students
ON enrollments.student_id = students.id

LEFT JOIN courses
ON students.course_id = courses.id

WHERE enrollments.payment_status='pending'
AND enrollments.STATUS='approved'

ORDER BY enrollments.created DESC
");

/* Dashboard Cards */

$totalPending = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) total
FROM enrollments
WHERE payment_status='pending'
AND STATUS='approved'
"))['total'];

$totalPaid = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) total
FROM enrollments
WHERE payment_status='paid'
"))['total'];

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cashier</title>

    <link rel="stylesheet" href="../css/admin_dashboard.css">

</head>

<body>

    <div class="dashboard-container">

        <?php include "navbar_layout.php"; ?>

        <div class="page-header">

            <h1>Cashier</h1>

            <p>Verify student payments</p>

        </div>

        <div class="stats-row">

            <div class="stat-card">

                <h3>Pending Payments</h3>

                <span><?php echo $totalPending; ?></span>

            </div>

            <div class="stat-card">

                <h3>Verified Payments</h3>

                <span><?php echo $totalPaid; ?></span>

            </div>

        </div>

        <div class="card">

            <div class="table-header">

                <h2>Payment Verification</h2>

                <input
                    type="text"
                    id="searchInput"
                    class="search-box"
                    placeholder="Search student...">

            </div>

            <table class="modern-table" id="cashierTable">

                <thead>

                    <tr>

                        <th>Student No.</th>

                        <th>Name</th>

                        <th>Course</th>

                        <th>Enrollment</th>

                        <th>Payment</th>

                        <th>Date</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>

                        <tr>

                            <td><?php echo $row['student_number']; ?></td>

                            <td>
                                <?php
                                echo $row['first_name'] . " " . $row['last_name'];
                                ?>
                            </td>

                            <td><?php echo $row['course_code']; ?></td>

                            <td>

                                <span class="status-approved">

                                    <?php echo ucfirst($row['STATUS']); ?>

                                </span>

                            </td>

                            <td>

                                <span class="status-pending">

                                    <?php echo ucfirst($row['payment_status']); ?>

                                </span>

                            </td>

                            <td>

                                <?php echo date("M d, Y", strtotime($row['created'])); ?>

                            </td>

                            <td>

                                <a
                                    href="../functions/verify_payment.php?id=<?php echo $row['id']; ?>"
                                    class="btn-edit"
                                    onclick="return confirm('Verify this payment?');">

                                    Verify Payment

                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {

            let value = this.value.toLowerCase();

            let rows = document.querySelectorAll("#cashierTable tbody tr");

            rows.forEach(function(row) {

                row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";

            });

        });
    </script>

</body>

</html>