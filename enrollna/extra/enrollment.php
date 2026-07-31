<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

session_start();
require "../db/dbconn.php";
require_once "../functions/enrollment_setup.php";

ensure_enrollment_schema($conn);
seed_course_data($conn);

// Require student login
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

// Load available courses with sections and subjects
$courses = mysqli_query($conn, "SELECT id, course_code, course_name FROM courses ORDER BY course_code ASC");
$course_catalog = [];

while ($course = mysqli_fetch_assoc($courses)) {
    $course_id = (int) $course['id'];

    $sections_result = mysqli_query($conn, "SELECT id, section_name FROM sections WHERE course_id = $course_id ORDER BY section_name ASC");
    $sections = [];
    while ($section = mysqli_fetch_assoc($sections_result)) {
        $sections[] = $section;
    }

    $subjects_result = mysqli_query($conn, "SELECT id, subject_code, subject_name, schedule_day, schedule_time, room_number FROM subjects WHERE course_id = $course_id ORDER BY subject_code ASC");
    $subjects = [];
    while ($subject = mysqli_fetch_assoc($subjects_result)) {
        $subjects[] = $subject;
    }

    $course_catalog[] = [
        'course' => $course,
        'sections' => $sections,
        'subjects' => $subjects,
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment</title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/enrollment.css">
</head>

<body>

    <div class="container my-5">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">College Enrollment Form</h3>
            </div>

            <div class="card-body">

                <form action="../functions/process_application.php" method="POST">

                    <!-- Logged in User -->
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']; ?>">

                    <h5 class="mb-3">Personal Information</h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input
                                type="text"
                                name="first_name"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input
                                type="text"
                                name="middle_name"
                                class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input
                                type="text"
                                name="last_name"
                                class="form-control"
                                required>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Birthdate</label>
                            <input
                                type="date"
                                name="birthdate"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sex</label>

                            <select
                                name="sex"
                                class="form-select"
                                required>

                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>

                            </select>

                        </div>

                    </div>

                    <hr>

                    <h5 class="mb-3">Contact Information</h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Email Address</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Contact Number</label>
                            <input
                                type="text"
                                name="contact"
                                maxlength="11"
                                class="form-control"
                                placeholder="09XXXXXXXXX"
                                required>
                        </div>

                    </div>

                    <div class="mb-3">

                        <label>Complete Address</label>

                        <textarea
                            name="address"
                            rows="3"
                            class="form-control"
                            required></textarea>

                    </div>

                    <hr>

                    <h5 class="mb-3">Guardian Information</h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label>Guardian Name</label>

                            <input
                                type="text"
                                name="guardian"
                                class="form-control"
                                required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Guardian Contact</label>

                            <input
                                type="text"
                                name="guardian_contact"
                                maxlength="11"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <hr>

                    <h5 class="mb-3">Program Information</h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label>Course</label>

                            <select
                                name="course_id"
                                class="form-select"
                                required>

                                <option value="">Select Course</option>

                                <?php foreach ($course_catalog as $entry) : $course = $entry['course']; ?>

                                    <option value="<?= (int) $course['id']; ?>">

                                        <?= htmlspecialchars($course['course_code']); ?>
                                        -
                                        <?= htmlspecialchars($course['course_name']); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Year Level</label>

                            <select
                                name="year_level"
                                class="form-select"
                                required>

                                <option value="">Select Year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>

                            </select>

                        </div>

                    </div>

                    <div class="mt-3">
                        <label class="form-label">Section and Subjects</label>
                        <p class="text-muted small">Choose a course to view its available section and subject options.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Section</label>
                                <select id="section_select" name="section_id" class="form-select" required>
                                    <option value="">Select Section</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Subjects</label>
                                <div id="subjectContainer" class="border rounded p-3">
                                    <p class="text-muted small mb-0">Select a course to load subjects.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enrollment Information -->

                    <input type="hidden" name="semester" value="1">
                    <input type="hidden" name="school_year" value="2026-2027">

                    <div class="text-center mt-4">
                        <a href="student_dashboard.php" class="back"><-Back</a>

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg">

                            Submit Enrollment
                        </button>
                        

                    </div>

                </form>

            </div>

        </div>

    </div>

    <script src="../js/bootstrap.bundle.js"></script>
    <script>
        const courseCatalog = <?= json_encode(array_map(function ($entry) {
            return [
                'id' => (int) $entry['course']['id'],
                'sections' => $entry['sections'],
                'subjects' => $entry['subjects'],
            ];
        }, $course_catalog), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const courseSelect = document.querySelector('select[name="course_id"]');
            const sectionSelect = document.getElementById('section_select');
            const subjectContainer = document.getElementById('subjectContainer');

            function renderCourseDetails() {
                const selectedCourseId = courseSelect ? courseSelect.value : '';
                const selectedCourse = courseCatalog.find(function (course) {
                    return String(course.id) === String(selectedCourseId);
                });

                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                subjectContainer.innerHTML = '<p class="text-muted small mb-0">Select a course to load subjects.</p>';

                if (!selectedCourse) {
                    return;
                }

                if (selectedCourse.sections && selectedCourse.sections.length > 0) {
                    selectedCourse.sections.forEach(function (section) {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.section_name;
                        sectionSelect.appendChild(option);
                    });
                } else {
                    sectionSelect.innerHTML = '<option value="">No sections available</option>';
                }

                if (selectedCourse.subjects && selectedCourse.subjects.length > 0) {
                    const subjectList = document.createElement('div');

                    selectedCourse.subjects.forEach(function (subject) {
                        const subjectItem = document.createElement('div');
                        subjectItem.className = 'form-check';
                        subjectItem.innerHTML = `
                            <input class="form-check-input" type="checkbox" name="subject_ids[]" value="${subject.id}" id="subject_${subject.id}">
                            <label class="form-check-label" for="subject_${subject.id}">
                                ${escapeHtml(subject.subject_code)} - ${escapeHtml(subject.subject_name)}
                                <span class="text-muted">(${escapeHtml(subject.schedule_day)} ${escapeHtml(subject.schedule_time)}, ${escapeHtml(subject.room_number)})</span>
                            </label>
                        `;
                        subjectList.appendChild(subjectItem);
                    });

                    subjectContainer.innerHTML = '';
                    subjectContainer.appendChild(subjectList);
                } else {
                    subjectContainer.innerHTML = '<p class="text-muted small mb-0">No subjects have been added for this course yet.</p>';
                }
            }

            if (courseSelect) {
                courseSelect.addEventListener('change', renderCourseDetails);
                renderCourseDetails();
            }
        });
    </script>

</body>

</html>