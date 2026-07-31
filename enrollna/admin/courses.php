<?php
require_once __DIR__ . '/../functions/admin_auth.php';
require_admin_login();

include '../db/dbconn.php';
require_once '../functions/enrollment_setup.php';

// Ensure is_available column exists
ensure_enrollment_schema($conn);

$selected_course = isset($_GET['course']) ? (int) $_GET['course'] : 0;

$coursesQuery = mysqli_query($conn, "
    SELECT
        courses.id,
        courses.course_code,
        courses.course_name,
        COUNT(sections.id) AS total_sections
    FROM courses
    LEFT JOIN sections ON courses.id = sections.course_id
    GROUP BY courses.id
    ORDER BY courses.course_code
");

$totalCourses  = mysqli_num_rows($coursesQuery);
$totalSections = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM sections'))['total'];
$totalSubjects = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM subjects'))['total'];
$availSubjects = (int) mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total FROM subjects WHERE is_available = 1'))['total'];

mysqli_data_seek($coursesQuery, 0);

$csrf = admin_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses – EYYSAT</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <style>
        /* ── Subject Availability Toggle ───────────────────── */
        .toggle-switch {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        .toggle-switch input { display: none; }
        .toggle-track {
            width: 46px; height: 24px;
            background: #3f3f5a;
            border-radius: 999px;
            position: relative;
            transition: background 0.3s;
            flex-shrink: 0;
        }
        .toggle-track::after {
            content: '';
            position: absolute;
            width: 18px; height: 18px;
            background: #fff;
            border-radius: 50%;
            top: 3px; left: 3px;
            transition: transform 0.3s;
            box-shadow: 0 1px 4px rgba(0,0,0,.3);
        }
        .toggle-switch input:checked + .toggle-track { background: #22c55e; }
        .toggle-switch input:checked + .toggle-track::after { transform: translateX(22px); }

        .badge-avail   { background: rgba(34,197,94,.18);  color:#22c55e;  border:1px solid rgba(34,197,94,.35);  }
        .badge-unavail { background: rgba(239,68,68,.15);  color:#f87171;  border:1px solid rgba(239,68,68,.3);   }
        .avail-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:.78rem; font-weight:700; }

        /* ── Course card tweaks ────────────────────────────── */
        .course-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:16px; margin-top:16px; }
        .course-card { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:20px; text-decoration:none; color:#fff; transition:.25s; }
        .course-card:hover { background:rgba(216,255,97,.08); border-color:#d8ff61; transform:translateY(-4px); }
        .course-card h2 { font-size:1.5rem; color:#d8ff61; margin-bottom:4px; }
        .course-card p  { font-size:.85rem; color:#a1a1aa; margin-bottom:10px; }
        .course-card span { display:inline-block; background:rgba(124,58,237,.4); color:#e0d4ff; font-size:.78rem; padding:4px 12px; border-radius:999px; }

        /* ── Subjects table ────────────────────────────────── */
        .subjects-table { width:100%; border-collapse:collapse; }
        .subjects-table thead th { background:#08021b; color:#d8ff61; padding:12px 14px; text-align:left; font-size:.82rem; letter-spacing:.05em; text-transform:uppercase; }
        .subjects-table tbody tr { border-bottom:1px solid rgba(255,255,255,.06); transition:.2s; }
        .subjects-table tbody tr:hover { background:rgba(255,255,255,.04); }
        .subjects-table td { padding:12px 14px; font-size:.92rem; vertical-align:middle; }

        .section-pill { display:inline-block; background:#1c056e; border:1px solid rgba(124,58,237,.4); border-radius:999px; padding:5px 14px; font-size:.85rem; color:#c4b5fd; }
    </style>
</head>
<body>
<div class="dashboard-container">

    <?php include 'navbar_layout.php'; ?>

    <!-- HERO -->
    <div class="hero">
        <div class="welcome">
            <span class="hero-badge">📚 Academic Programs</span>
            <h1>Courses</h1>
            <p>Manage academic programs, sections, and subject availability.</p>
        </div>

        <div class="hero-stats">
            <div class="mini-stat">
                <h3>Courses</h3>
                <span><?= $totalCourses ?></span>
            </div>
            <div class="mini-stat">
                <h3>Sections</h3>
                <span><?= $totalSections ?></span>
            </div>
            <div class="mini-stat">
                <h3>Subjects</h3>
                <span><?= $availSubjects ?> / <?= $totalSubjects ?> <small style="font-size:.6em;opacity:.7;">available</small></span>
            </div>
        </div>
    </div>

    <div class="analytics-grid" style="grid-template-columns:1fr;">

        <!-- COURSE CARDS -->
        <div class="card">
            <h3>Available Courses</h3>
            <div class="course-grid">
                <?php while ($course = mysqli_fetch_assoc($coursesQuery)) { ?>
                    <a href="courses.php?course=<?= (int) $course['id'] ?>" class="course-card">
                        <h2><?= htmlspecialchars($course['course_code']) ?></h2>
                        <p><?= htmlspecialchars($course['course_name']) ?></p>
                        <span><?= (int) $course['total_sections'] ?> Sections</span>
                    </a>
                <?php } ?>
            </div>
        </div>

        <?php if ($selected_course > 0):
            $courseInfo = mysqli_fetch_assoc(mysqli_query($conn,
                'SELECT * FROM courses WHERE id = ' . $selected_course));
            if ($courseInfo):
        ?>

        <!-- COURSE DETAIL -->
        <div class="card">
            <h3><?= htmlspecialchars($courseInfo['course_code']) ?>
                <span style="font-size:.75em;color:#a1a1aa;font-weight:400;">
                    — <?= htmlspecialchars($courseInfo['course_name']) ?>
                </span>
            </h3>

            <!-- Sections -->
            <div style="margin:16px 0 8px;">
                <h4 style="color:#d8ff61;margin-bottom:12px;">Sections</h4>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <?php
                    $sections = mysqli_query($conn,
                        'SELECT * FROM sections WHERE course_id = ' . $selected_course . ' ORDER BY section_name');
                    while ($section = mysqli_fetch_assoc($sections)): ?>
                        <div class="section-pill">
                            <?= htmlspecialchars($section['section_name']) ?>
                            <span style="opacity:.6;font-size:.8em;"> · <?= (int) $section['max_slots'] ?> slots</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <hr style="border-color:rgba(255,255,255,.08);margin:20px 0;">

            <!-- Subjects with availability toggle -->
            <h4 style="color:#d8ff61;margin-bottom:14px;">Subjects
                <span style="color:#a1a1aa;font-weight:400;font-size:.8em;">(toggle to enable/disable for enrollment)</span>
            </h4>

            <div style="overflow-x:auto;">
                <table class="subjects-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Units</th>
                            <th>Year</th>
                            <th>Sem</th>
                            <th>Schedule</th>
                            <th>Room</th>
                            <th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subjects = mysqli_query($conn,
                            'SELECT * FROM subjects WHERE course_id = ' . $selected_course . ' ORDER BY year_level, sem, subject_code');
                        while ($subj = mysqli_fetch_assoc($subjects)):
                            $available = (bool) $subj['is_available'];
                        ?>
                        <tr>
                            <td><strong style="color:#d8ff61;"><?= htmlspecialchars($subj['subject_code']) ?></strong></td>
                            <td><?= htmlspecialchars($subj['subject_name']) ?></td>
                            <td style="text-align:center;"><?= (int) $subj['units'] ?></td>
                            <td style="text-align:center;"><?= (int) $subj['year_level'] ?></td>
                            <td style="text-align:center;"><?= (int) $subj['sem'] ?></td>
                            <td>
                                <?= htmlspecialchars($subj['schedule_day'] ?? '—') ?>
                                <?= htmlspecialchars($subj['schedule_time'] ?? '') ?>
                            </td>
                            <td><?= htmlspecialchars($subj['room_number'] ?? '—') ?></td>
                            <td>
                                <!-- Availability toggle (POST form) -->
                                <form method="POST" action="../functions/toggle_subject.php"
                                      style="display:inline-flex;align-items:center;gap:10px;">
                                    <input type="hidden" name="csrf_token"  value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="subject_id"  value="<?= (int) $subj['id'] ?>">
                                    <input type="hidden" name="course_id"   value="<?= $selected_course ?>">
                                    <label class="toggle-switch" title="<?= $available ? 'Click to disable' : 'Click to enable' ?>">
                                        <input type="checkbox"
                                               <?= $available ? 'checked' : '' ?>
                                               onchange="this.closest('form').submit()">
                                        <span class="toggle-track"></span>
                                    </label>
                                    <span class="avail-badge <?= $available ? 'badge-avail' : 'badge-unavail' ?>">
                                        <?= $available ? 'Available' : 'Unavailable' ?>
                                    </span>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif; endif; ?>

    </div>
</div>

<script>
window.addEventListener('pageshow', e => { if (e.persisted) window.location.reload(); });
</script>
</body>
</html>
