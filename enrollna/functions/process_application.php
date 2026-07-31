<?php
require_once __DIR__ . '/student_auth.php';
start_student_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../enrollment.php', true, 303);
    exit;
}

require_student_csrf();
require '../db/dbconn.php';
require_once 'enrollment_setup.php';

ensure_enrollment_schema($conn);
seed_course_data($conn);

function enrollment_error(string $message): void
{
    $_SESSION['enrollment_error'] = $message;
    header('Location: ../enrollment.php', true, 303);
    exit;
}

$first_name        = trim((string) ($_POST['first_name'] ?? ''));
$middle_name       = trim((string) ($_POST['middle_name'] ?? ''));
$last_name         = trim((string) ($_POST['last_name'] ?? ''));
$sex               = (string) ($_POST['sex'] ?? '');
$birthdate         = (string) ($_POST['birthdate'] ?? '');
$address           = trim((string) ($_POST['address'] ?? ''));
$contact           = trim((string) ($_POST['contact'] ?? ''));
$email             = trim((string) ($_POST['email'] ?? ''));
$guardian          = trim((string) ($_POST['guardian'] ?? ''));
$guardian_contact  = trim((string) ($_POST['guardian_contact'] ?? ''));
$course_id         = (int) ($_POST['course_id'] ?? 0);
$section_id        = (int) ($_POST['section_id'] ?? 0);
$subject_ids       = array_values(array_unique(array_filter(array_map('intval', (array) ($_POST['subject_ids'] ?? [])))));
$year_level        = (int) ($_POST['year_level'] ?? 0);
$school_year       = '2026-2027';
$semester          = '1';

// Payments are collected only after the application has been approved.
$payment_option    = '';
$payment_method    = '';
$payment_reference = '';
$payment_amount    = 0.00;

$birthdateValue = DateTimeImmutable::createFromFormat('!Y-m-d', $birthdate);
$isAdult = $birthdateValue !== false
    && $birthdateValue->format('Y-m-d') === $birthdate
    && $birthdateValue <= (new DateTimeImmutable('today'))->modify('-18 years');
$validMiddleInitials = $middle_name === '' || (bool) preg_match('/^[A-Za-z. ]{1,5}$/', $middle_name);

if ($first_name === '' || $last_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $address === '' || $contact === '' || $guardian === '' || $guardian_contact === '' || !in_array($sex, ['Male', 'Female'], true) || !$isAdult || !$validMiddleInitials || !in_array($year_level, [1, 2, 3, 4], true) || $course_id < 1 || $section_id < 1 || $subject_ids === []) {
    enrollment_error('Please complete all required details. Applicants must be at least 18 years old and use middle initials only.');
}

$user_id = null;
if (($_SESSION['role'] ?? '') === 'student' && !empty($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
}

// Check for existing student record for this user or email
$existing_student_id = null;
if ($user_id !== null) {
    $chk_user = mysqli_prepare($conn, 'SELECT id FROM students WHERE user_id = ? LIMIT 1');
    mysqli_stmt_bind_param($chk_user, 'i', $user_id);
    mysqli_stmt_execute($chk_user);
    $res_u = mysqli_stmt_get_result($chk_user);
    if ($row = mysqli_fetch_assoc($res_u)) {
        $existing_student_id = (int) $row['id'];
    }
    mysqli_stmt_close($chk_user);
}

if ($existing_student_id === null) {
    $chk_email = mysqli_prepare($conn, 'SELECT id FROM students WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($chk_email, 's', $email);
    mysqli_stmt_execute($chk_email);
    $res_e = mysqli_stmt_get_result($chk_email);
    if ($row = mysqli_fetch_assoc($res_e)) {
        $existing_student_id = (int) $row['id'];
    }
    mysqli_stmt_close($chk_email);
}

if ($user_id === null && $existing_student_id !== null) {
    enrollment_error('An application already exists for this email. Please sign in to view your profile.');
}

if ($user_id === null) {
    $account = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($account, 's', $email);
    mysqli_stmt_execute($account);
    mysqli_stmt_store_result($account);
    if (mysqli_stmt_num_rows($account) > 0) {
        mysqli_stmt_close($account);
        enrollment_error('An account already exists for this email. Please sign in before submitting enrollment.');
    }
    mysqli_stmt_close($account);
}

// Balance calculation
$total_tuition = 15000.00;
$remaining_balance = max(0.00, $total_tuition - $payment_amount);
$payment_status = ($remaining_balance <= 0.00) ? 'Paid' : 'Pending';

mysqli_begin_transaction($conn);
try {
    // Validate section
    $section_check = mysqli_prepare($conn, 'SELECT id FROM sections WHERE id = ? AND course_id = ?');
    mysqli_stmt_bind_param($section_check, 'ii', $section_id, $course_id);
    mysqli_stmt_execute($section_check);
    mysqli_stmt_store_result($section_check);
    if (mysqli_stmt_num_rows($section_check) === 0) {
        throw new RuntimeException('The selected section is invalid.');
    }
    mysqli_stmt_close($section_check);

    // Validate subjects
    foreach ($subject_ids as $subject_id) {
        $subject_check = mysqli_prepare($conn, 'SELECT id FROM subjects WHERE id = ? AND course_id = ?');
        mysqli_stmt_bind_param($subject_check, 'ii', $subject_id, $course_id);
        mysqli_stmt_execute($subject_check);
        mysqli_stmt_store_result($subject_check);
        if (mysqli_stmt_num_rows($subject_check) === 0) {
            throw new RuntimeException('One of the selected subjects is invalid.');
        }
        mysqli_stmt_close($subject_check);
    }

    if ($existing_student_id !== null) {
        // UPDATE existing student record
        $student_id = $existing_student_id;
        $upd_student = mysqli_prepare($conn, '
            UPDATE students SET
                user_id = ?,
                first_name = ?,
                middle_name = ?,
                last_name = ?,
                sex = ?,
                birthdate = ?,
                address = ?,
                contact = ?,
                email = ?,
                guardian = ?,
                guardian_contact = ?,
                course_id = ?,
                section_id = ?,
                year_level = ?,
                remaining_balance = ?
            WHERE id = ?
        ');
        mysqli_stmt_bind_param($upd_student, 'issssssssssiiidi',
            $user_id, $first_name, $middle_name, $last_name, $sex, $birthdate,
            $address, $contact, $email, $guardian, $guardian_contact,
            $course_id, $section_id, $year_level, $remaining_balance, $student_id
        );
        mysqli_stmt_execute($upd_student);
        mysqli_stmt_close($upd_student);
    } else {
        // INSERT new student record
        $student_number = generate_student_number($conn, $course_id);
        $student_stmt = mysqli_prepare($conn, '
            INSERT INTO students (
                user_id, student_number, first_name, middle_name, last_name,
                sex, birthdate, address, contact, email, guardian, guardian_contact,
                course_id, section_id, year_level, payment_status, enrollment_status, remaining_balance
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \'Pending\', ?)
        ');
        mysqli_stmt_bind_param($student_stmt, 'isssssssssssiiisd',
            $user_id, $student_number, $first_name, $middle_name, $last_name,
            $sex, $birthdate, $address, $contact, $email, $guardian, $guardian_contact,
            $course_id, $section_id, $year_level, $payment_status, $remaining_balance
        );
        mysqli_stmt_execute($student_stmt);
        $student_id = mysqli_insert_id($conn);
        mysqli_stmt_close($student_stmt);
    }

    // Insert into enrollments saving ALL payment details (option, method, reference, amount)
    $enrollment_stmt = mysqli_prepare($conn, "
        INSERT INTO enrollments (
            student_id, school_year, sem, status, payment_status,
            payment_method, payment_option, payment_reference, amount_paid
        ) VALUES (?, ?, ?, 'Pending', ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($enrollment_stmt, 'issssssd',
        $student_id, $school_year, $semester, $payment_status,
        $payment_method, $payment_option, $payment_reference, $payment_amount
    );
    mysqli_stmt_execute($enrollment_stmt);
    $enrollment_id = mysqli_insert_id($conn);
    mysqli_stmt_close($enrollment_stmt);

    // Insert selected subjects into enrollment_details
    $subject_stmt = mysqli_prepare($conn, 'INSERT INTO enrollment_details (enrollment_id, subject_id) VALUES (?, ?)');
    foreach ($subject_ids as $subject_id) {
        mysqli_stmt_bind_param($subject_stmt, 'ii', $enrollment_id, $subject_id);
        mysqli_stmt_execute($subject_stmt);
    }
    mysqli_stmt_close($subject_stmt);

    mysqli_commit($conn);
} catch (Throwable $exception) {
    mysqli_rollback($conn);
    enrollment_error('Unable to submit the application. Please review your details and try again.');
}

unset($_SESSION['student_csrf_token']);
$_SESSION['enrollment_success'] = $user_id === null
    ? 'Application submitted. Create an account with the same email to access your profile.'
    : 'Enrollment submitted successfully.';

header('Location: ' . ($user_id === null ? '../auth/register.php?from=enrollment' : '../student/profile.php'), true, 303);
exit;
