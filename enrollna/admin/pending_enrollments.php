<?php
session_start();
require "../db/dbconn.php";

if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit();
}


$sql = "
SELECT
    s.id,
    s.student_number,
    s.first_name,
    s.middle_name,
    s.last_name,
    c.course_code,
    c.course_name,
    s.year_level,
    s.payment_status,
    s.enrollment_status,
    e.school_year,
    e.sem
FROM students s
INNER JOIN enrollments e
    ON s.id = e.student_id
INNER JOIN courses c
    ON s.course_id = c.id
WHERE s.enrollment_status = 'Pending'
ORDER BY s.created ASC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Pending Enrollments</title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <script src="../js/sweetalert2.all.min.js"></script>
    <script src="../js/swal-confirm.js" defer></script>

</head>

<body>

    <div class="container mt-5">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                <h3>Pending Enrollment Applications</h3>

            </div>

            <div class="card-body">

                <?php if (mysqli_num_rows($result) > 0) { ?>

                    <table class="table table-bordered table-hover">

                        <thead class="table-dark">

                            <tr>

                                <th>Student No.</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Semester</th>
                                <th>School Year</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th width="180">Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                <tr>

                                    <td>

                                        <?= $row['student_number'] ?? 'Not Assigned'; ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $row['last_name'] . ", " .
                                                $row['first_name'] . " " .
                                                $row['middle_name']
                                        ); ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $row['course_code'] . " - " .
                                                $row['course_name']
                                        ); ?>

                                    </td>

                                    <td>

                                        <?= $row['year_level']; ?>

                                    </td>

                                    <td>

                                        <?= $row['sem']; ?>

                                    </td>

                                    <td>

                                        <?= $row['school_year']; ?>

                                    </td>

                                    <td>

                                        <?= $row['payment_status']; ?>

                                    </td>

                                    <td>

                                        <span class="badge bg-warning text-dark">

                                            <?= $row['enrollment_status']; ?>

                                        </span>

                                    </td>

                                    <td>

                                        <form action="approve_student.php" method="POST" class="d-inline">

                                            <input
                                                type="hidden"
                                                name="student_id"
                                                value="<?= $row['id']; ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-success btn-sm">

                                                Approve

                                            </button>

                                        </form>

                                        <a
                                            href="reject_student.php?id=<?= $row['id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            data-swal-confirm="Reject this application?"
                                            data-swal-title="Reject application?">

                                            Reject

                                        </a>

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                <?php } else { ?>

                    <div class="alert alert-success">

                        No pending enrollment applications.

                    </div>

                <?php } ?>

            </div>

        </div>

    </div>

</body>

</html>
