<?php
require_once __DIR__ . '/../functions/student_auth.php';
require_student_login();

include '../db/dbconn.php';
require_once '../functions/enrollment_setup.php';

ensure_enrollment_schema($conn);
seed_course_data($conn);

$user_id = (int) $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT
        students.*,
        courses.course_name,
        courses.course_code,
        sections.section_name
    FROM students
    LEFT JOIN courses  ON students.course_id  = courses.id
    LEFT JOIN sections ON students.section_id = sections.id
    WHERE students.user_id = $user_id
");

$student = mysqli_fetch_assoc($query);

if (!$student) {
    http_response_code(404);
    exit('Student profile not found.');
}

$enrollment_query = mysqli_query($conn, "
    SELECT status, payment_status, school_year, sem
    FROM enrollments
    WHERE student_id = " . (int) $student['id'] . "
    ORDER BY id DESC
    LIMIT 1
");
$enrollment = mysqli_fetch_assoc($enrollment_query) ?: [];

$subject_query = mysqli_query($conn, "
    SELECT
        subjects.subject_code,
        subjects.subject_name,
        subjects.schedule_day,
        subjects.schedule_time,
        subjects.room_number
    FROM enrollment_details
    LEFT JOIN enrollments ON enrollments.id = enrollment_details.enrollment_id
    LEFT JOIN subjects    ON subjects.id    = enrollment_details.subject_id
    WHERE enrollments.student_id = " . (int) $student['id'] . "
    ORDER BY subjects.subject_code ASC
");

$selected_subjects = [];
while ($subject = mysqli_fetch_assoc($subject_query)) {
    $selected_subjects[] = $subject;
}

// Determine if Pay Now button should show
$canPay = (
    strtolower($enrollment['status'] ?? '') === 'approved' &&
    strtolower($enrollment['payment_status'] ?? '') !== 'paid' &&
    ((float) ($student['remaining_balance'] ?? 0)) > 0
);
$displayEnrollmentStatus = (
    strtolower($enrollment['status'] ?? '') === 'approved' &&
    strtolower($enrollment['payment_status'] ?? '') === 'paid'
) ? 'Enrolled' : ($enrollment['status'] ?? $student['enrollment_status'] ?? 'Not Submitted');

$csrf = student_csrf_token();

// Payment notification from redirect
$paymentMsg = '';
if (isset($_GET['payment'])) {
    if ($_GET['payment'] === 'success') {
        $paymentMsg = 'success';
    } elseif ($_GET['payment'] === 'failed') {
        $paymentMsg = 'failed';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>My Profile – Eyysat</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/student_profile.css">
    <script src="../js/sweetalert2.all.min.js"></script>
    <style>
        .cash-payment-kiosk {
            width: min(420px, calc(100vw - 32px));
            max-height: calc(100vh - 32px);
            margin: auto;
            padding: 24px;
            overflow-y: auto;
            color: #fff;
            background: var(--academy-blue, #0d3b66);
            border: 2px solid var(--academy-yellow, #f4d35e);
            border-radius: 16px;
            box-shadow: 0 16px 50px rgba(0, 0, 0, .3);
        }

        .cash-payment-kiosk::backdrop { background: rgba(12, 28, 46, .6); }
        .cash-payment-kiosk input { color: #17324d; background: #fff; }
        .cash-payment-kiosk .btn-link { color: var(--academy-yellow, #f4d35e); }
        .cash-payment-kiosk__choices { display: grid; gap: 10px; }
        .cash-payment-kiosk__custom { display: flex; gap: 8px; margin-top: 6px; }

        @media (max-width: 480px) {
            .cash-payment-kiosk { padding: 20px; }
            .cash-payment-kiosk__custom { flex-direction: column; }
            .cash-payment-kiosk__custom .btn { width: 100%; }
        }
    </style>
</head>

<body class="profile-page">

    <div class="dashboard-container">

        <!-- NAVBAR -->
        <div class="navbar">
            <div class="logo">
                <img src="../img/eyysat.png" alt="EYYSAT Logo" class="logo-image">
                <span class="logo-text">EYYSAT</span>
            </div>

            <div class="nav-menu">
                <a href="../webhome.php">Home</a>
                <a href="profile.php" class="active">Profile</a>
            </div>

            <div class="profile">
                <?= htmlspecialchars($_SESSION['fullname'] ?? '') ?>
                <a href="../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- HERO -->
        <div class="hero">
            <div class="welcome">
                <span class="eyebrow">🎓 Student Portal</span>
                <h1>Student Profile</h1>
                <p>Your complete student record, enrollment progress, and account details.</p>

                <div class="student-id">
                    <?php if (!empty($student['profile_picture'])): ?>
                        <div class="student-avatar">
                            <img src="../uploads/<?= htmlspecialchars($student['profile_picture']) ?>"
                                alt="Profile Picture">
                        </div>
                    <?php else: ?>
                        <div class="student-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <strong><?= htmlspecialchars($student['student_number'] ?? 'Pending') ?></strong>
                        <span>Student Number</span>
                    </div>
                </div>
            </div>

            <div class="hero-stats">
                <div class="mini-stat">
                    <h3>Course</h3>
                    <span><?= htmlspecialchars($student['course_code'] ?? 'N/A') ?></span>
                </div>

                <div class="mini-stat">
                    <h3>Balance</h3>
                    <span>₱<?= number_format((float)($student['remaining_balance'] ?? 0), 2) ?></span>
                </div>

                <div class="mini-stat">
                    <h3>Status</h3>
                        <span><?= htmlspecialchars($displayEnrollmentStatus) ?></span>
                </div>
            </div>
        </div>

        <!-- PAYMENT NOTIFICATION -->
        <?php if ($paymentMsg === 'success'): ?>
            <div class="alert-banner alert-success">
                🎉 <strong>Payment successful!</strong> Your enrollment fee has been received.
                Your account will be updated shortly.
            </div>
        <?php elseif ($paymentMsg === 'failed'): ?>
            <div class="alert-banner alert-danger">
                ⚠️ <strong>Payment was not completed.</strong> Please try again or contact the cashier.
            </div>
        <?php endif; ?>

        <!-- CARDS GRID -->
        <div class="analytics-grid">

            <!-- Personal Information -->
            <div class="card">
                <h3>Personal Information</h3>

                <form action="../functions/student_update_profile.php" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($csrf) ?>">

                    <div class="profile-pic-upload">
                        <?php if (!empty($student['profile_picture'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($student['profile_picture']) ?>" alt="Profile Picture" class="upload-preview" id="pic_preview">
                        <?php else: ?>
                            <div class="upload-preview-placeholder" id="pic_placeholder"><i class="fa-solid fa-camera"></i></div>
                            <img src="" alt="Profile Picture" class="upload-preview" id="pic_preview" style="display:none;">
                        <?php endif; ?>
                        <div class="upload-controls">
                            <label for="profile_picture_input" class="custom-file-upload">
                                📤 Choose Profile Picture
                            </label>
                            <input id="profile_picture_input" type="file" name="profile_picture" accept="image/*" style="display:none;" onchange="previewImage(this)">
                            <small class="text-muted" style="font-size: 0.72rem;">PNG, JPG or JPEG up to 2MB</small>
                        </div>
                    </div>

                    <div class="form-row-grid">
                        <div>
                            <label>First Name</label>
                            <input type="text" name="first_name"
                                value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label>Middle Name</label>
                            <input type="text" name="middle_name"
                                value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
                        </div>
                        <div>
                            <label>Last Name</label>
                            <input type="text" name="last_name"
                                value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-row-grid-2">
                        <div>
                            <label>Email</label>
                            <input type="email" name="email"
                                value="<?= htmlspecialchars($student['email'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label>Contact Number</label>
                            <input type="text" name="contact"
                                value="<?= htmlspecialchars($student['contact'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label>Address</label>
                        <textarea name="address" rows="3" required><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
                    </div>

                    <button class="btn" type="submit" style="width:100%; margin-top:10px;">Save Changes</button>
                </form>
            </div>

            <!-- Academic Information -->
            <div class="card">
                <h3>Academic Information</h3>

                <dl class="info-list">
                    <div>
                        <dt>Student No.</dt>
                        <dd><?= htmlspecialchars($student['student_number'] ?? 'Pending') ?></dd>
                    </div>
                    <div>
                        <dt>Course</dt>
                        <dd><?= htmlspecialchars($student['course_name'] ?? 'Not selected') ?></dd>
                    </div>
                    <div>
                        <dt>Course Code</dt>
                        <dd><?= htmlspecialchars($student['course_code'] ?? 'N/A') ?></dd>
                    </div>
                    <div>
                        <dt>Year Level</dt>
                        <dd><?= htmlspecialchars($student['year_level'] ?? 'N/A') ?></dd>
                    </div>
                    <div>
                        <dt>Birthdate</dt>
                        <dd><?= htmlspecialchars($student['birthdate'] ?? 'N/A') ?></dd>
                    </div>
                    <div>
                        <dt>Sex</dt>
                        <dd><?= htmlspecialchars($student['sex'] ?? 'N/A') ?></dd>
                    </div>
                    <div>
                        <dt>Section</dt>
                        <dd><?= htmlspecialchars($student['section_name'] ?? 'Not assigned yet') ?></dd>
                    </div>
                </dl>

                <?php if (!empty($selected_subjects)): ?>
                    <h3 style="margin-top:1.5rem;">Selected Subjects</h3>
                    <div style="overflow-x:auto;">
                        <table class="subjects-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Subject Name</th>
                                    <th>Schedule</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selected_subjects as $subject): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($subject['subject_code']) ?></td>
                                        <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                                        <td><?= htmlspecialchars($subject['schedule_day']) ?> <?= htmlspecialchars($subject['schedule_time']) ?></td>
                                        <td><?= htmlspecialchars($subject['room_number']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color:#888;margin-top:1rem;">No subjects selected yet.</p>
                <?php endif; ?>

                <div style="margin-top:1.5rem;">
                    <h3>Registration Form</h3>
                    <p style="color:#666;margin-bottom:.75rem;">View your official submitted registration details.</p>
                    <a href="registration_form_view.php" target="_blank" class="btn btn-doc">
                        📄 View Registration Form
                    </a>
                </div>
            </div>

            <div class="card schedule-card" id="schedule" style="grid-column: 1 / -1;">
                <div class="schedule-heading">
                    <div>
                        <h3>My Schedule</h3>
                        <p>Your enrolled subjects for the current term.</p>
                    </div>
                    <span class="schedule-term"><?= htmlspecialchars($enrollment['school_year'] ?? 'Current Term') ?></span>
                </div>

                <?php if (!empty($selected_subjects)): ?>
                    <div class="weekly-schedule" aria-label="Weekly class schedule">
                        <?php foreach (['Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday'] as $dayCode => $dayName): ?>
                            <section class="schedule-day">
                                <h4><?= $dayName ?></h4>
                                <?php $hasClass = false; ?>
                                <?php foreach ($selected_subjects as $subject): ?>
                                    <?php if (in_array(strtolower((string) $subject['schedule_day']), [strtolower($dayCode), strtolower($dayName)], true)): ?>
                                        <?php $hasClass = true; ?>
                                        <article class="schedule-entry">
                                            <strong><?= htmlspecialchars($subject['subject_code']) ?></strong>
                                            <span><?= htmlspecialchars($subject['subject_name']) ?></span>
                                            <small><?= htmlspecialchars($subject['schedule_time']) ?> · Room <?= htmlspecialchars($subject['room_number']) ?></small>
                                        </article>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if (!$hasClass): ?><p class="schedule-empty">No classes</p><?php endif; ?>
                            </section>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="schedule-empty">Your class schedule will appear after subjects are selected.</p>
                <?php endif; ?>
            </div>

            <!-- Enrollment Status -->
            <div class="card" id="enrollment" style="grid-column: 1 / -1;">
                <h3>Enrollment Status</h3>

                <div class="status-summary">
                    <div class="status-item">
                        <span>Application</span>
                        <strong class="<?= in_array(strtolower($displayEnrollmentStatus), ['approved', 'enrolled'], true) ? 'text-success' : (strtolower($displayEnrollmentStatus) === 'rejected' ? 'text-danger' : 'text-warning') ?>">
                            <?= htmlspecialchars($displayEnrollmentStatus) ?>
                        </strong>
                    </div>
                    <div class="status-item">
                        <span>Payment</span>
                        <strong class="<?= strtolower($enrollment['payment_status'] ?? '') === 'paid' ? 'text-success' : 'text-warning' ?>">
                            <?= htmlspecialchars($enrollment['payment_status'] ?? $student['payment_status'] ?? 'Pending') ?>
                        </strong>
                    </div>
                    <div class="status-item">
                        <span>School Year</span>
                        <strong><?= htmlspecialchars($enrollment['school_year'] ?? 'N/A') ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Semester</span>
                        <strong><?= htmlspecialchars($enrollment['sem'] ?? 'N/A') ?></strong>
                    </div>
                </div>

                <h3 style="margin-top:1.5rem;">Application Progress</h3>
                <div class="progress">
                    <div class="step active">1. Account Created</div>
                    <div class="step <?= $enrollment ? 'active' : '' ?>">2. Application Submitted</div>
                    <div class="step <?= (strtolower($enrollment['status'] ?? '') === 'approved') ? 'active' : '' ?>">3. Approved</div>
                    <div class="step <?= ((strtolower($enrollment['status'] ?? '') === 'approved') && (strtolower($enrollment['payment_status'] ?? '') === 'paid')) ? 'active' : '' ?>">4. Enrolled</div>
                </div>

                <?php if (strtolower($enrollment['payment_status'] ?? '') === 'paid'): ?>
                    <div class="pay-now-box">
                        <div class="pay-now-info">
                            <h4>Payment</h4>
                            <p>Your enrollment fee has been fully paid and verified.</p>
                        </div>
                        <button class="btn btn-success" type="button" disabled>Fully Paid</button>
                    </div>
                <?php elseif ($canPay): ?>
                    <!-- PAY NOW SECTION -->
                    <div class="pay-now-box">
                        <div class="pay-now-info">
                            <h4>💳 Ready to Pay Your Enrollment Fee?</h4>
                            <p>
                                Your enrollment is approved. Complete your payment of
                                <strong>₱<?= number_format((float)$student['remaining_balance'], 2) ?></strong>
                                securely via GCash, Maya, or Credit/Debit Card.
                            </p>
                        </div>
                        <button id="cashPaymentBtn" class="btn btn-secondary" type="button" onclick="openCashKiosk()" style="margin-right:8px;">
                            Pay at Cashier
                        </button>
                        <button id="payNowBtn" class="btn btn-pay" onclick="initiatePayment()">
                            <span id="payBtnText">💳 Pay Now via PayMongo</span>
                            <span id="payBtnSpinner" style="display:none;">⏳ Redirecting…</span>
                        </button>
                    </div>

                    <!-- Hidden CSRF form data for fetch -->
                    <input type="hidden" id="paycsrf" value="<?= htmlspecialchars($csrf) ?>">

                    <dialog id="cashPaymentKiosk" class="cash-payment-kiosk">
                        <h3 style="margin-top:0;">Cash Payment Kiosk</h3>
                        <p>Select the amount you will pay at the cashier.</p>
                        <div class="cash-payment-kiosk__choices">
                            <button class="btn btn-pay" type="button" onclick="selectCashPayment(2500)">Downpayment — ₱2,500</button>
                            <button class="btn btn-pay" type="button" onclick="selectCashPayment(15000)">Full Payment — ₱15,000</button>
                        </div>
                        <label for="customCashAmount" style="display:block;margin-top:16px;font-weight:600;">Custom amount</label>
                        <div class="cash-payment-kiosk__custom">
                            <input id="customCashAmount" type="number" min="1" step="0.01" class="form-control" placeholder="Enter amount">
                            <button class="btn btn-secondary" type="button" onclick="submitCustomCashPayment()">Submit</button>
                        </div>
                        <button class="btn btn-link" type="button" onclick="closeCashKiosk()" style="margin-top:12px;">Cancel</button>
                    </dialog>
                <?php elseif ($enrollment): ?>
                    <div class="pay-now-box">
                        <div class="pay-now-info">
                            <h4>Payment</h4>
                            <p>Your application is currently <strong><?= htmlspecialchars(ucfirst($enrollment['status'] ?? 'pending')) ?></strong>. Payment options will appear here once an administrator approves your enrollment.</p>
                        </div>
                        <button class="btn btn-secondary" type="button" disabled>Waiting for Approval</button>
                    </div>
                <?php endif; ?>

                <h3 style="margin-top:1.5rem;">Important Notices</h3>
                <ul class="notice-list">
                    <li>Wait for admin approval of your application before paying.</li>
                    <li>Once approved, use the <strong>Pay Now</strong> button to pay online — GCash, Maya, or Card accepted.</li>
                    <li>Cash payments are verified by the cashier in person.</li>
                    <li>Keep your contact details up to date.</li>
                </ul>
            </div>

        </div>

    </div>

    <!-- PAYMENT ERROR TOAST -->
    <div id="payErrorToast" style="
        display:none; position:fixed; bottom:24px; right:24px;
        background:#ef4444; color:#fff; padding:14px 20px;
        border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,.25);
        font-weight:600; z-index:9999; max-width:340px;">
    </div>

    <script>
        /* ── Back-button / tab security ─────────────────────────── */
        window.addEventListener('pageshow', function(e) {
            if (e.persisted) window.location.reload();
        });

        /* ── PayMongo Pay Now ───────────────────────────────────── */
        async function initiatePayment() {
            const btn = document.getElementById('payNowBtn');
            const txtEl = document.getElementById('payBtnText');
            const spinEl = document.getElementById('payBtnSpinner');
            const csrf = document.getElementById('paycsrf')?.value || '';

            btn.disabled = true;
            txtEl.style.display = 'none';
            spinEl.style.display = 'inline';

            try {
                const res = await fetch('../functions/create_payment_link.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'csrf_token=' + encodeURIComponent(csrf),
                });

                const data = await res.json();

                if (data.url) {
                    window.location.href = data.url;
                } else {
                    showPayError(data.error || 'Could not create a payment link. Try again.');
                    btn.disabled = false;
                    txtEl.style.display = 'inline';
                    spinEl.style.display = 'none';
                }
            } catch (err) {
                showPayError('Network error. Please check your connection and try again.');
                btn.disabled = false;
                txtEl.style.display = 'inline';
                spinEl.style.display = 'none';
            }
        }

        function openCashKiosk() {
            const kiosk = document.getElementById('cashPaymentKiosk');
            if (kiosk?.showModal) kiosk.showModal();
        }

        function closeCashKiosk() {
            document.getElementById('cashPaymentKiosk')?.close();
        }

        function submitCustomCashPayment() {
            const amount = Number(document.getElementById('customCashAmount')?.value || 0);
            if (amount <= 0) {
                showPayError('Enter a valid cash amount.');
                return;
            }
            selectCashPayment(amount);
        }

        async function selectCashPayment(amount) {
            const btn = document.getElementById('cashPaymentBtn');
            const csrf = document.getElementById('paycsrf')?.value || '';
            btn.disabled = true;

            try {
                const res = await fetch('../functions/select_cash_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'csrf_token=' + encodeURIComponent(csrf) + '&cash_amount=' + encodeURIComponent(amount),
                });
                const data = await res.json();

                if (res.ok) {
                    closeCashKiosk();
                    Swal.fire({
                        icon: 'info',
                        title: 'Cash payment requested',
                        text: 'Please wait for the cashier to verify your payment after you pay.',
                        confirmButtonText: 'OK'
                    });
                } else {
                    showPayError(data.error || 'Could not select cash payment. Try again.');
                }
            } catch (err) {
                showPayError('Network error. Please check your connection and try again.');
            } finally {
                btn.disabled = false;
            }
        }

        function showPayError(msg) {
            const toast = document.getElementById('payErrorToast');
            toast.textContent = msg;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 5000);
        }

        /* ── Profile Image Preview ──────────────────────────────── */
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('pic_preview');
                    const placeholder = document.getElementById('pic_placeholder');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>
