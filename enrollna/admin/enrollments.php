<?php
require_once __DIR__ . '/../functions/admin_auth.php';
require_admin_login();

include '../db/dbconn.php';

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
        enrollments.amount_paid,
        enrollments.created
    FROM enrollments
    INNER JOIN students ON enrollments.student_id = students.id
    LEFT JOIN  courses  ON students.course_id = courses.id
    ORDER BY enrollments.created DESC
");

$pending  = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM enrollments WHERE STATUS='pending'"))['total'];
$approved = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM enrollments WHERE STATUS='approved'"))['total'];
$rejected = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM enrollments WHERE STATUS='rejected'"))['total'];

$csrf = admin_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management – EYYSAT</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <script src="../js/sweetalert2.all.min.js"></script>
    <script src="../js/swal-confirm.js" defer></script>
</head>
<body>
<div class="dashboard-container">

    <?php include 'navbar_layout.php'; ?>

    <div class="page-header">
        <h1>Enrollment Management</h1>
        <p>Review and approve student enrollment applications.</p>
    </div>

    <div class="stats-row">
        <div class="stat-card stat-warning">
            <div class="stat-icon">⏳</div>
            <h3>Pending</h3>
            <span><?= $pending ?></span>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon">✅</div>
            <h3>Approved</h3>
            <span><?= $approved ?></span>
        </div>
        <div class="stat-card stat-delete">
            <div class="stat-icon">❌</div>
            <h3>Rejected</h3>
            <span><?= $rejected ?></span>
        </div>
    </div>

    <div class="card">
        <div class="table-header">
            <h2>Enrollment Records</h2>
            <input type="text" id="searchInput" class="search-box" placeholder="Search student…">
        </div>
        <div style="overflow-x:auto;">
            <table class="modern-table" id="enrollTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student No.</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>School Year</th>
                        <th>Sem</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)) {
                    $status = strtolower($row['STATUS']);
                    $isEnrolled = $status === 'approved' && (float) ($row['amount_paid'] ?? 0) >= 2500;
                ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><?= htmlspecialchars($row['student_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['course_code']) ?></td>
                        <td><?= htmlspecialchars($row['school_year']) ?></td>
                        <td><?= htmlspecialchars($row['sem']) ?></td>
                        <td>
                            <?php if ($isEnrolled): ?>
                                <span class="status-approved">Enrolled</span>
                            <?php elseif ($status === 'approved'): ?>
                                <span class="status-approved">Approved</span>
                            <?php elseif ($status === 'pending'): ?>
                                <span class="status-pending">Pending</span>
                            <?php else: ?>
                                <span class="status-rejected">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(date('M d, Y', strtotime($row['created']))) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="student_info.php?id=<?= (int)$row['id'] ?>" class="btn-view">View</a>

                                <?php if ($status === 'pending'): ?>
                                    <!-- Approve -->
                                    <form method="POST" action="../functions/update_enrollment.php"
                                          data-swal-confirm="Approve this enrollment?" data-swal-title="Approve enrollment?"
                                          style="display:inline;">
                                        <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="enrollment_id" value="<?= (int)$row['id'] ?>">
                                        <input type="hidden" name="status"        value="approved">
                                        <button type="submit" class="btn-edit">Approve</button>
                                    </form>
                                    <!-- Reject -->
                                    <form method="POST" action="../functions/update_enrollment.php"
                                          data-swal-confirm="Reject this enrollment?" data-swal-title="Reject enrollment?"
                                          style="display:inline;">
                                        <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="enrollment_id" value="<?= (int)$row['id'] ?>">
                                        <input type="hidden" name="status"        value="rejected">
                                        <button type="submit" class="btn-delete">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <span class="completed-text">Completed</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#enrollTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
    });
});
window.addEventListener('pageshow', e => { if (e.persisted) window.location.reload(); });
</script>
</body>
</html>
