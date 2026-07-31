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
        enrollments.payment_status,
        enrollments.cash_amount_requested,
        enrollments.paymongo_link_id,
        enrollments.STATUS,
        enrollments.created
    FROM enrollments
    INNER JOIN students ON enrollments.student_id = students.id
    LEFT JOIN  courses  ON students.course_id = courses.id
    WHERE enrollments.payment_status = 'pending'
      AND enrollments.STATUS = 'approved'
    ORDER BY enrollments.created DESC
");

$totalPending = (int) mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM enrollments WHERE payment_status='pending' AND STATUS='approved'"))['total'];

$totalPaid = (int) mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM enrollments WHERE payment_status='paid'"))['total'];

$csrf = admin_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier – EYYSAT</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <script src="../js/sweetalert2.all.min.js"></script>
    <script src="../js/swal-confirm.js" defer></script>
</head>
<body>
<div class="dashboard-container">

    <?php include 'navbar_layout.php'; ?>

    <div class="page-header">
        <h1>Cashier</h1>
        <p>Verify student payments — manual (cash) and online (PayMongo)</p>
    </div>

    <div class="stats-row">
        <div class="stat-card stat-warning">
            <div class="stat-icon">⏳</div>
            <h3>Pending Payments</h3>
            <span><?= $totalPending ?></span>
        </div>
        <div class="stat-card stat-paid">
            <div class="stat-icon">✅</div>
            <h3>Verified Payments</h3>
            <span><?= $totalPaid ?></span>
        </div>
    </div>

    <div class="card">
        <div class="table-header">
            <h2>Payment Verification</h2>
            <input type="text" id="searchInput" class="search-box" placeholder="Search student…">
        </div>

        <div style="overflow-x:auto;">
            <table class="modern-table" id="cashierTable">
                <thead>
                    <tr>
                        <th>Student No.</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Enrollment</th>
                        <th>Payment</th>
                        <th>Cash Requested</th>
                        <th>PayMongo</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['course_code']) ?></td>
                        <td><span class="status-approved"><?= htmlspecialchars(ucfirst($row['STATUS'])) ?></span></td>
                        <td><span class="status-pending"><?= htmlspecialchars(ucfirst($row['payment_status'])) ?></span></td>
                        <td><?= $row['cash_amount_requested'] !== null ? '₱' . number_format((float) $row['cash_amount_requested'], 2) : '—' ?></td>
                        <td>
                            <?php if (!empty($row['paymongo_link_id'])): ?>
                                <span class="badge bg-info" title="<?= htmlspecialchars($row['paymongo_link_id']) ?>">
                                    Online ✓
                                </span>
                            <?php else: ?>
                                <span style="color:#888;font-size:.85em;">Cash / Manual</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(date('M d, Y', strtotime($row['created']))) ?></td>
                        <td>
                            <!-- POST form with CSRF — no plain GET link -->
                            <form method="POST" action="../functions/verify_payment.php"
                                  data-swal-confirm="Verify the requested cash amount?" data-swal-title="Verify payment?">
                                <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                                <input type="hidden" name="enrollment_id" value="<?= (int)$row['id'] ?>">
                                <button type="submit" class="btn-edit">Verify (Cash)</button>
                            </form>
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
    document.querySelectorAll('#cashierTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
    });
});

window.addEventListener('pageshow', e => { if (e.persisted) window.location.reload(); });
</script>
</body>
</html>
