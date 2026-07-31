<?php
require_once __DIR__ . '/../functions/admin_auth.php';
require_admin_login();

include '../db/dbconn.php';

// Safe search with prepared statement
$search = trim((string) ($_GET['search'] ?? ''));
$like   = '%' . $search . '%';

$totalStudents = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM students'))['total'];
$csrf = admin_csrf_token();

$stmt = mysqli_prepare($conn, "
    SELECT
        students.id,
        students.student_number,
        students.first_name,
        students.last_name,
        students.email,
        students.year_level,
        students.payment_status,
        courses.course_code,
        sections.section_name
    FROM students
    LEFT JOIN courses  ON students.course_id  = courses.id
    LEFT JOIN sections ON students.section_id = sections.id
    WHERE students.student_number LIKE ?
       OR students.last_name LIKE ?
       OR students.email LIKE ?
    ORDER BY students.id DESC
");
mysqli_stmt_bind_param($stmt, 'sss', $like, $like, $like);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students – EYYSAT</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <script src="../js/sweetalert2.all.min.js"></script>
    <script src="../js/swal-confirm.js" defer></script>
</head>
<body>
<div class="dashboard-container">

    <?php include 'navbar_layout.php'; ?>

    <div class="page-header">
        <h1>Students</h1>
        <p>Manage registered students</p>
    </div>

    <div class="stats-row">
        <div class="stat-card stat-success">
            <div class="stat-icon">🎓</div>
            <h3>Total Students</h3>
            <span><?= $totalStudents ?></span>
        </div>
        <div class="stat-card stat-section">
            <div class="stat-icon">📋</div>
            <h3>Shown in Table</h3>
            <span><?= mysqli_num_rows($query) ?></span>
        </div>
    </div>

    <div class="card">
        <div class="table-header">
            <h2>Student List</h2>
            <input type="text" id="searchInput" class="search-box"
                   placeholder="Search student…" value="<?= htmlspecialchars($search) ?>">
        </div>

        <div style="overflow-x:auto;">
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
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><?= htmlspecialchars($row['student_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['course_code'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['year_level'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['section_name'] ?? '—') ?></td>
                        <td><?= strtolower($row['payment_status'] ?? '') === 'paid' ? 'Fully Paid' : htmlspecialchars($row['payment_status'] ?? 'Pending') ?></td>
                        <td>
                            <a href="edit_student.php?id=<?= (int)$row['id'] ?>" class="btn-edit">Edit</a>
                            <form method="POST" action="../controller/delete_student.php" style="display:inline;"
                                  data-swal-confirm="Delete this student permanently?" data-swal-title="Delete student?">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                <button type="submit" class="btn-delete">Delete</button>
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
    document.querySelectorAll('#studentTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
    });
});
window.addEventListener('pageshow', e => { if (e.persisted) window.location.reload(); });
</script>
</body>
</html>
