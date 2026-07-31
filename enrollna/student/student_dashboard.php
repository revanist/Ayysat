<?php
require_once __DIR__ . '/../functions/student_auth.php';
require_student_login();

// Student information is now kept in one place: the profile page.
header('Location: profile.php', true, 303);
exit;
