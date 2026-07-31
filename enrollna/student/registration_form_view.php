<?php
require_once __DIR__ . '/../functions/student_auth.php';
require_student_login();

include "../db/dbconn.php";
$user_id = (int) $_SESSION['user_id'];

$query = mysqli_query($conn, "
SELECT
    students.*,
    courses.course_name,
    courses.course_code,
    sections.section_name
FROM students
LEFT JOIN courses
ON students.course_id = courses.id
LEFT JOIN sections
ON students.section_id = sections.id
WHERE students.user_id=$user_id
");
$student = mysqli_fetch_assoc($query);

if (!$student) {
    die("Registration details not found. Please enroll first.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f4f4f4; }
        .form-container { background: #fff; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; color: #666; }
        .section { margin-bottom: 30px; }
        .section-title { background: #eee; padding: 10px; font-weight: bold; border-left: 5px solid #f39c12; margin-bottom: 15px; }
        .info-row { display: flex; margin-bottom: 10px; }
        .info-label { width: 150px; font-weight: bold; color: #555; }
        .info-value { flex: 1; border-bottom: 1px solid #ccc; padding-left: 10px; }
        .print-btn { display: block; width: 200px; margin: 30px auto 0; padding: 10px; background: #0d6efd; color: #fff; text-align: center; text-decoration: none; border-radius: 5px; cursor: pointer; border: none; font-size: 16px; }
        @media print {
            body { background: #fff; padding: 0; }
            .form-container { box-shadow: none; max-width: 100%; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <h1>Official Registration Form</h1>
            <p>EYYSAT Academy</p>
        </div>
        
        <div class="section">
            <div class="section-title">Academic Details</div>
            <div class="info-row"><div class="info-label">Student Number:</div><div class="info-value"><?php echo htmlspecialchars($student['student_number']); ?></div></div>
            <div class="info-row"><div class="info-label">Course:</div><div class="info-value"><?php echo htmlspecialchars($student['course_name'] ?? ''); ?> (<?php echo htmlspecialchars($student['course_code'] ?? ''); ?>)</div></div>
            <div class="info-row"><div class="info-label">Year Level:</div><div class="info-value"><?php echo htmlspecialchars($student['year_level']); ?></div></div>
            <div class="info-row"><div class="info-label">Status:</div><div class="info-value"><?php echo htmlspecialchars($student['enrollment_status']); ?></div></div>
        </div>

        <div class="section">
            <div class="section-title">Personal Information</div>
            <div class="info-row"><div class="info-label">Full Name:</div><div class="info-value"><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' ' . $student['middle_name']); ?></div></div>
            <div class="info-row"><div class="info-label">Birthdate:</div><div class="info-value"><?php echo htmlspecialchars($student['birthdate']); ?></div></div>
            <div class="info-row"><div class="info-label">Sex:</div><div class="info-value"><?php echo htmlspecialchars($student['sex']); ?></div></div>
            <div class="info-row"><div class="info-label">Address:</div><div class="info-value"><?php echo htmlspecialchars($student['address']); ?></div></div>
            <div class="info-row"><div class="info-label">Contact Number:</div><div class="info-value"><?php echo htmlspecialchars($student['contact']); ?></div></div>
            <div class="info-row"><div class="info-label">Email:</div><div class="info-value"><?php echo htmlspecialchars($student['email']); ?></div></div>
        </div>

        <button class="print-btn" onclick="window.print()">Print Form</button>
    </div>
</body>
</html>
