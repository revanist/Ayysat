<?php
require_once __DIR__ . '/../functions/student_auth.php';
require_student_login();

// Enrollment status is displayed on the consolidated profile page.
header('Location: profile.php#enrollment', true, 303);
exit;
