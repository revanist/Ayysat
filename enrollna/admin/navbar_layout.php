<?php
/**
 * Shared admin navigation bar.
 * Dynamic active-link detection uses basename of the current file.
 */
$currentPage = basename($_SERVER['PHP_SELF']);
function nav_active(string $file, string $current): string {
    return $current === $file ? ' class="active"' : '';
}
?>
<div class="navbar">
    <div class="logo">
        <img src="../img/eyysat.png" alt="EYYSAT Logo" class="logo-image">
        <span class="logo-text">EYYSAT</span>
    </div>

    <div class="nav-menu">
        <a href="admin_dashboard.php"<?= nav_active('admin_dashboard.php', $currentPage) ?>>Dashboard</a>
        <a href="students.php"<?= nav_active('students.php', $currentPage) ?>>Students</a>
        <a href="courses.php"<?= nav_active('courses.php', $currentPage) ?>>Courses</a>
        <a href="enrollments.php"<?= nav_active('enrollments.php', $currentPage) ?>>Enrollments</a>
        <a href="cashier.php"<?= nav_active('cashier.php', $currentPage) ?>>Cashier</a>
        <a href="reports.php"<?= nav_active('reports.php', $currentPage) ?>>Reports</a>
    </div>

    <div class="profile">
        <span><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
        <a href="../auth/admin_logout.php" class="logout-btn">Logout</a>
    </div>
</div>
