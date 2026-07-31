<?php
require_once __DIR__ . '/../functions/admin_auth.php';
require_admin_login();

include '../db/dbconn.php';

$students    = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM students'))['total'];
$courses     = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM courses'))['total'];
$subjects    = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM subjects'))['total'];
$enrollments = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM enrollments'))['total'];

$pendingPayments = (int) (mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM enrollments WHERE payment_status='pending'"))['total'] ?? 0);

$approved = (int) (mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM enrollments WHERE STATUS='approved'"))['total'] ?? 0);

$paid = (int) (mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM enrollments WHERE payment_status='paid'"))['total'] ?? 0);

$sections = (int) (mysqli_fetch_assoc(mysqli_query($conn,
    'SELECT COUNT(*) total FROM sections'))['total'] ?? 0);

// Monthly enrollment data for the bar chart (last 6 months)
$monthlyData  = [];
$monthLabels  = [];
for ($i = 5; $i >= 0; $i--) {
    $ts           = strtotime("-$i months");
    $monthLabels[] = date('M Y', $ts);
    $y            = date('Y', $ts);
    $m            = date('m', $ts);
    $r = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) total FROM enrollments
         WHERE YEAR(created)='$y' AND MONTH(created)='$m'"));
    $monthlyData[] = (int) ($r['total'] ?? 0);
}

$adminName = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – EYYSAT</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="dashboard-container">

    <?php include 'navbar_layout.php'; ?>

    <!-- HERO -->
    <div class="hero">
        <div class="welcome">
            <span class="hero-badge">📊 Dashboard Overview</span>
            <h1>Welcome back, <span class="accent"><?= $adminName ?></span></h1>
            <p>Enrollment Management System — real-time overview of all activity.</p>
            <div class="hero-big-stat">
                <h2 class="count-up" data-target="<?= $students ?>">0</h2>
                <span>Total Students Registered</span>
            </div>
        </div>

        <div class="hero-stats">
            <div class="mini-stat">
                <h3>Courses</h3>
                <span class="count-up" data-target="<?= $courses ?>"><?= $courses ?></span>
            </div>
            <div class="mini-stat">
                <h3>Subjects</h3>
                <span class="count-up" data-target="<?= $subjects ?>"><?= $subjects ?></span>
            </div>
            <div class="mini-stat">
                <h3>Enrollments</h3>
                <span class="count-up" data-target="<?= $enrollments ?>"><?= $enrollments ?></span>
            </div>
        </div>
    </div>

    <!-- QUICK STATS ROW -->
    <div class="stats-row">
        <div class="stat-card stat-warning">
            <div class="stat-icon">⏳</div>
            <h3>Pending Payments</h3>
            <span class="count-up" data-target="<?= $pendingPayments ?>"><?= $pendingPayments ?></span>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon">✅</div>
            <h3>Approved Enrollments</h3>
            <span class="count-up" data-target="<?= $approved ?>"><?= $approved ?></span>
        </div>
        <div class="stat-card stat-paid">
            <div class="stat-icon">💰</div>
            <h3>Fully Paid</h3>
            <span class="count-up" data-target="<?= $paid ?>"><?= $paid ?></span>
        </div>
        <div class="stat-card stat-section">
            <div class="stat-icon">🏫</div>
            <h3>Sections</h3>
            <span class="count-up" data-target="<?= $sections ?>"><?= $sections ?></span>
        </div>
    </div>

    <!-- ANALYTICS GRID -->
    <div class="analytics-grid">

        <!-- Doughnut chart -->
        <div class="card chart-card">
            <h3>System Overview</h3>
            <canvas id="enrollmentChart"></canvas>
        </div>

        <!-- Bar chart -->
        <div class="card chart-card">
            <h3>Monthly Enrollments</h3>
            <canvas id="monthlyChart"></canvas>
        </div>

        <!-- Recent Enrollments -->
        <div class="card recent-card">
            <h3>Recent Enrollments</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Student No.</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentQuery = mysqli_query($conn, "
                            SELECT
                                students.student_number,
                                students.first_name,
                                students.last_name,
                                courses.course_code,
                                enrollments.payment_status,
                                enrollments.amount_paid,
                                enrollments.STATUS
                            FROM enrollments
                            INNER JOIN students ON enrollments.student_id = students.id
                            LEFT JOIN courses   ON students.course_id = courses.id
                            ORDER BY enrollments.id DESC
                            LIMIT 10
                        ");
                        while ($row = mysqli_fetch_assoc($recentQuery)) {
                            $hasPayment = (float) ($row['amount_paid'] ?? 0) > 0;
                            $payClass = (strtolower($row['payment_status']) === 'paid' || $hasPayment)
                                ? 'status-paid' : 'status-pending';
                            $envClass = strtolower($row['STATUS']) === 'approved'
                                ? 'status-approved' : (strtolower($row['STATUS']) === 'rejected'
                                    ? 'status-rejected' : 'status-pending');
                            $enrollmentLabel = (
                                strtolower($row['STATUS']) === 'approved'
                                && (strtolower($row['payment_status']) === 'paid' || $hasPayment)
                            ) ? 'Enrolled' : ucfirst($row['STATUS']);
                            $paymentLabel = strtolower($row['payment_status']) === 'paid'
                                ? 'Fully Paid'
                                : ($hasPayment ? 'Paid' : ucfirst($row['payment_status']));
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['student_number']) ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['course_code']) ?></td>
                            <td><span class="<?= $payClass ?>"><?= htmlspecialchars($paymentLabel) ?></span></td>
                            <td><span class="<?= $envClass ?>"><?= htmlspecialchars($enrollmentLabel) ?></span></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
/* ── Animated count-up ─────────────────────────── */
document.querySelectorAll('.count-up').forEach(el => {
    const target = +el.dataset.target;
    const duration = 1200;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = Math.floor(current).toLocaleString();
        if (current >= target) clearInterval(timer);
    }, 16);
});

/* ── Doughnut chart ────────────────────────────── */
new Chart(document.getElementById('enrollmentChart'), {
    type: 'doughnut',
    data: {
        labels: ['Students', 'Courses', 'Subjects', 'Enrollments'],
        datasets: [{
            data: [<?= $students ?>, <?= $courses ?>, <?= $subjects ?>, <?= $enrollments ?>],
            backgroundColor: ['#f4d35e', '#1e5a96', '#3b82f6', '#f39c12'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { labels: { color: 'white', font: { size: 13 } } }
        }
    }
});

/* ── Bar chart ─────────────────────────────────── */
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($monthLabels) ?>,
        datasets: [{
            label: 'Enrollments',
            data: <?= json_encode($monthlyData) ?>,
            backgroundColor: 'rgba(244, 211, 94, 0.75)',
            borderColor: '#f4d35e',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: 'white' } }
        },
        scales: {
            x: { ticks: { color: '#a1a1aa' }, grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { ticks: { color: '#a1a1aa', stepSize: 1 }, grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true }
        }
    }
});

/* ── Prevent cached page after logout ──────────── */
window.addEventListener('pageshow', function(e) {
    if (e.persisted) window.location.reload();
});
</script>

</body>
</html>
