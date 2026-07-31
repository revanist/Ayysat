<?php
require_once __DIR__ . '/../functions/admin_auth.php';
require_admin_login();

include '../db/dbconn.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, 'SELECT * FROM students WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    http_response_code(404);
    exit('Student not found.');
}

$csrf = admin_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student – <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></title>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>
<body>
<div class="dashboard-container">

    <?php include 'navbar_layout.php'; ?>

    <div class="page-header">
        <a href="students.php" style="color:var(--lime); text-decoration:none; font-weight:700; font-size:.9rem; display:inline-block; margin-bottom:8px;">
            ← Back to Students
        </a>
        <h1>Edit Student Profile</h1>
        <p>Update personal information for student #<?= htmlspecialchars($student['student_number'] ?? 'Pending') ?></p>
    </div>

    <div class="analytics-grid" style="grid-template-columns:1fr; max-width:760px; margin:0 auto;">
        <div class="card">
            <h3>Student Details Editor</h3>

            <form action="../controller/update_student.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                    <div>
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
                    </div>

                    <div>
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
                    </div>

                    <div>
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-top:12px;">
                    <div>
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($student['email'] ?? '') ?>" required>
                    </div>

                    <div>
                        <label>Contact Number</label>
                        <input type="text" name="contact" value="<?= htmlspecialchars($student['contact'] ?? '') ?>" required>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <label>Home Address</label>
                    <textarea name="address" rows="3" required><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
                </div>

                <div style="margin-top:24px; display:flex; gap:12px;">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="students.php" class="btn btn-secondary" style="background:rgba(255,255,255,.1); color:#fff; text-decoration:none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
window.addEventListener('pageshow', e => { if (e.persisted) window.location.reload(); });
</script>
</body>
</html>