<?php
require_once __DIR__ . '/../functions/admin_auth.php';
require_admin_login();

include '../db/dbconn.php';

$id = (int) ($_GET['id'] ?? 0);

// Try finding by enrollment id first
$query = mysqli_query($conn, "
    SELECT
        e.id AS enrollment_id,
        e.status AS enrollment_status_col,
        e.payment_status AS enrollment_payment_status,
        e.school_year,
        e.sem,
        e.created AS enrollment_created,
        e.paymongo_link_id,
        e.payment_method,
        e.payment_option,
        e.payment_reference,
        e.amount_paid,
        s.id AS student_db_id,
        s.student_number,
        s.first_name,
        s.middle_name,
        s.last_name,
        s.sex,
        s.birthdate,
        s.address,
        s.contact,
        s.email,
        s.guardian,
        s.guardian_contact,
        s.profile_picture,
        s.year_level,
        s.remaining_balance,
        s.payment_status AS student_pay_status,
        s.enrollment_status AS student_env_status,
        c.course_name,
        c.course_code,
        sec.section_name
    FROM enrollments e
    INNER JOIN students s ON e.student_id = s.id
    LEFT JOIN courses c ON s.course_id = c.id
    LEFT JOIN sections sec ON s.section_id = sec.id
    WHERE e.id = $id
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    // Fallback: check if id is student_db_id
    $query_student = mysqli_query($conn, "
        SELECT
            s.id AS student_db_id,
            s.student_number,
            s.first_name,
            s.middle_name,
            s.last_name,
            s.sex,
            s.birthdate,
            s.address,
            s.contact,
            s.email,
            s.guardian,
            s.guardian_contact,
            s.profile_picture,
            s.year_level,
            s.remaining_balance,
            s.payment_status AS student_pay_status,
            s.enrollment_status AS student_env_status,
            c.course_name,
            c.course_code,
            sec.section_name,
            e.id AS enrollment_id,
            e.status AS enrollment_status_col,
            e.payment_status AS enrollment_payment_status,
            e.school_year,
            e.sem,
            e.created AS enrollment_created,
            e.paymongo_link_id,
            e.payment_method,
            e.payment_option,
            e.payment_reference,
            e.amount_paid
        FROM students s
        LEFT JOIN courses c ON s.course_id = c.id
        LEFT JOIN sections sec ON s.section_id = sec.id
        LEFT JOIN enrollments e ON e.student_id = s.id
        WHERE s.id = $id
        ORDER BY e.id DESC
        LIMIT 1
    ");
    $data = mysqli_fetch_assoc($query_student);
}

if (!$data) {
    http_response_code(404);
    exit('Student record not found.');
}

// Fetch enrolled subjects
$selected_subjects = [];
if (!empty($data['enrollment_id'])) {
    $sub_q = mysqli_query($conn, "
        SELECT
            subjects.subject_code,
            subjects.subject_name,
            subjects.units,
            subjects.schedule_day,
            subjects.schedule_time,
            subjects.room_number
        FROM enrollment_details
        INNER JOIN subjects ON subjects.id = enrollment_details.subject_id
        WHERE enrollment_details.enrollment_id = " . (int)$data['enrollment_id'] . "
        ORDER BY subjects.subject_code ASC
    ");
    while ($sub = mysqli_fetch_assoc($sub_q)) {
        $selected_subjects[] = $sub;
    }
}

$csrf = admin_csrf_token();
$appStatus = strtolower($data['enrollment_status_col'] ?? $data['student_env_status'] ?? 'pending');
$payStatus = strtolower($data['enrollment_payment_status'] ?? $data['student_pay_status'] ?? 'pending');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details – <?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></title>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <script src="../js/sweetalert2.all.min.js"></script>
    <script src="../js/swal-confirm.js" defer></script>
</head>
<body>
<div class="dashboard-container">

    <?php include 'navbar_layout.php'; ?>

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
        <div>
            <a href="enrollments.php" style="color:var(--lime); text-decoration:none; font-weight:700; font-size:.9rem; display:inline-block; margin-bottom:8px;">
                ← Back to Enrollments
            </a>
            <h1>Student Profile Details</h1>
            <p>Comprehensive overview of applicant information, academics, and enrollment status.</p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="edit_student.php?id=<?= (int)$data['student_db_id'] ?>" class="btn-warning" style="padding:10px 18px; border-radius:10px; font-size:.9rem;">
                ✏️ Edit Student Info
            </a>
            <?php if ($appStatus === 'pending'): ?>
                <form method="POST" action="../functions/update_enrollment.php" data-swal-confirm="Approve this enrollment?" data-swal-title="Approve enrollment?" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="enrollment_id" value="<?= (int)$data['enrollment_id'] ?>">
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn-edit" style="padding:10px 18px; border-radius:10px; font-size:.9rem;">✓ Approve Application</button>
                </form>
                <form method="POST" action="../functions/update_enrollment.php" data-swal-confirm="Reject this enrollment?" data-swal-title="Reject enrollment?" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="enrollment_id" value="<?= (int)$data['enrollment_id'] ?>">
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn-delete" style="padding:10px 18px; border-radius:10px; font-size:.9rem;">✗ Reject Application</button>
                </form>
            <?php endif; ?>
            <?php if ($payStatus !== 'paid' && $appStatus === 'approved'): ?>
                <form method="POST" action="../functions/verify_payment.php" data-swal-confirm="Verify the requested cash amount?" data-swal-title="Verify payment?" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="enrollment_id" value="<?= (int)$data['enrollment_id'] ?>">
                    <button type="submit" class="btn-pay" style="padding:10px 18px; font-size:.9rem;">💰 Verify Payment (Cash)</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- STUDENT HERO CARD -->
    <div class="welcome" style="margin-bottom:26px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;">
        <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
            <?php if (!empty($data['profile_picture'])): ?>
                <img src="../uploads/<?= htmlspecialchars($data['profile_picture']) ?>" alt="Profile Picture" style="width:95px; height:95px; border-radius:50%; object-fit:cover; border:3px solid var(--lime); box-shadow:0 8px 24px rgba(0,0,0,.4);">
            <?php else: ?>
                <div style="width:95px; height:95px; border-radius:50%; background:rgba(255,255,255,.1); display:grid; place-items:center; font-size:2.4rem; color:var(--lime); border:3px solid var(--lime); box-shadow:0 8px 24px rgba(0,0,0,.4);">
                    <i class="fa-solid fa-user"></i>
                </div>
            <?php endif; ?>
            <div>
                <span class="hero-badge">Student ID: <?= htmlspecialchars($data['student_number'] ?? 'Pending') ?></span>
                <h1 style="margin:4px 0; font-size:2.2rem;">
                    <?= htmlspecialchars($data['first_name'] . ' ' . ($data['middle_name'] ? $data['middle_name'] . ' ' : '') . $data['last_name']) ?>
                </h1>
                <p style="margin:0; color:var(--text-muted);">
                    <i class="fa-solid fa-graduation-cap" style="color:var(--lime);"></i> <?= htmlspecialchars($data['course_name'] ?? 'No course assigned') ?> (<?= htmlspecialchars($data['course_code'] ?? 'N/A') ?>)
                </p>
            </div>
        </div>

        <div style="display:flex; gap:16px; flex-wrap:wrap;">
            <div class="mini-stat" style="min-width:140px;">
                <h3>Application</h3>
                <span class="<?= $appStatus === 'approved' ? 'status-approved' : ($appStatus === 'rejected' ? 'status-rejected' : 'status-pending') ?>" style="font-size:1.4rem;">
                    <?= htmlspecialchars(ucfirst($appStatus)) ?>
                </span>
            </div>
            <div class="mini-stat" style="min-width:140px;">
                <h3>Payment</h3>
                <span class="<?= $payStatus === 'paid' ? 'status-paid' : 'status-pending' ?>" style="font-size:1.4rem;">
                    <?= $payStatus === 'paid' ? 'Fully Paid' : htmlspecialchars(ucfirst($payStatus)) ?>
                </span>
            </div>
            <div class="mini-stat" style="min-width:140px;">
                <h3>Remaining Balance</h3>
                <span style="font-size:1.4rem; color:var(--lime);">
                    ₱<?= number_format((float)($data['remaining_balance'] ?? 0), 2) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- DETAILS GRID -->
    <div class="analytics-grid">

        <!-- PERSONAL INFORMATION -->
        <div class="card">
            <h3>Personal Information</h3>
            <ul class="info-list" style="grid-template-columns:1fr 1fr;">
                <div>
                    <dt>Full Name</dt>
                    <dd><?= htmlspecialchars($data['first_name'] . ' ' . ($data['middle_name'] ? $data['middle_name'] . ' ' : '') . $data['last_name']) ?></dd>
                </div>
                <div>
                    <dt>Email Address</dt>
                    <dd><?= htmlspecialchars($data['email'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt>Contact Number</dt>
                    <dd><?= htmlspecialchars($data['contact'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt>Sex</dt>
                    <dd><?= htmlspecialchars($data['sex'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt>Date of Birth</dt>
                    <dd><?= htmlspecialchars($data['birthdate'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt>Student Number</dt>
                    <dd><?= htmlspecialchars($data['student_number'] ?? 'Pending') ?></dd>
                </div>
                <div style="grid-column:1 / -1;">
                    <dt>Home Address</dt>
                    <dd><?= htmlspecialchars($data['address'] ?? '—') ?></dd>
                </div>
            </ul>
        </div>

        <!-- ACADEMIC & EMERGENCY CONTACT -->
        <div class="card">
            <h3>Academic & Guardian Info</h3>
            <ul class="info-list" style="grid-template-columns:1fr 1fr;">
                <div>
                    <dt>Course Program</dt>
                    <dd><?= htmlspecialchars($data['course_name'] ?? 'Not assigned') ?> (<?= htmlspecialchars($data['course_code'] ?? 'N/A') ?>)</dd>
                </div>
                <div>
                    <dt>Year Level</dt>
                    <dd><?= htmlspecialchars($data['year_level'] ? $data['year_level'] . ' Year' : 'N/A') ?></dd>
                </div>
                <div>
                    <dt>Assigned Section</dt>
                    <dd><?= htmlspecialchars($data['section_name'] ?? 'Not assigned') ?></dd>
                </div>
                <div>
                    <dt>School Year / Sem</dt>
                    <dd><?= htmlspecialchars(($data['school_year'] ?? '2026-2027') . ' (Sem ' . ($data['sem'] ?? '1') . ')') ?></dd>
                </div>
                <div>
                    <dt>Guardian Name</dt>
                    <dd><?= htmlspecialchars($data['guardian'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt>Guardian Contact</dt>
                    <dd><?= htmlspecialchars($data['guardian_contact'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt>Payment Option</dt>
                    <dd><?= htmlspecialchars(ucfirst($data['payment_option'] ?? '—')) ?></dd>
                </div>
                <div>
                    <dt>Payment Method</dt>
                    <dd><?= htmlspecialchars(ucfirst($data['payment_method'] ?? '—')) ?></dd>
                </div>
                <div>
                    <dt>Payment Reference</dt>
                    <dd><?= htmlspecialchars($data['payment_reference'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt>Initial Amount Paid</dt>
                    <dd>₱<?= number_format((float)($data['amount_paid'] ?? 0), 2) ?></dd>
                </div>
                <div style="grid-column:1 / -1;">
                    <dt>PayMongo Link ID</dt>
                    <dd><?= htmlspecialchars($data['paymongo_link_id'] ?? 'None (Manual Cash / Pending)') ?></dd>
                </div>
            </ul>
        </div>

        <!-- ENROLLED SUBJECTS TABLE -->
        <div class="card" style="grid-column:1 / -1;">
            <h3>Enrolled / Selected Subjects</h3>
            <?php if (!empty($selected_subjects)): ?>
                <div style="overflow-x:auto;">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Description</th>
                                <th>Units</th>
                                <th>Schedule Day</th>
                                <th>Schedule Time</th>
                                <th>Room Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($selected_subjects as $subj): ?>
                                <tr>
                                    <td><strong style="color:var(--lime);"><?= htmlspecialchars($subj['subject_code']) ?></strong></td>
                                    <td><?= htmlspecialchars($subj['subject_name']) ?></td>
                                    <td style="text-align:center;"><?= (int)$subj['units'] ?></td>
                                    <td><?= htmlspecialchars($subj['schedule_day'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($subj['schedule_time'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($subj['room_number'] ?? '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color:var(--text-muted); padding:10px 0;">No subject details recorded for this enrollment application.</p>
            <?php endif; ?>
        </div>

    </div>

</div>

<script>
window.addEventListener('pageshow', e => { if (e.persisted) window.location.reload(); });
</script>
</body>
</html>
