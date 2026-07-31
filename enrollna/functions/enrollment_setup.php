<?php
function ensure_enrollment_schema($conn)
{
    $tables = [];

    $tables['students'] = [
        ['name' => 'section_id', 'definition' => 'INT NULL DEFAULT NULL'],
        ['name' => 'profile_picture', 'definition' => 'VARCHAR(255) NULL DEFAULT NULL'],
        ['name' => 'remaining_balance', 'definition' => 'DECIMAL(10,2) NULL DEFAULT 0.00'],
    ];

    $tables['subjects'] = [
        ['name' => 'section_id',    'definition' => 'INT NULL DEFAULT NULL'],
        ['name' => 'schedule_day',  'definition' => 'VARCHAR(20) NULL DEFAULT NULL'],
        ['name' => 'schedule_time', 'definition' => 'VARCHAR(50) NULL DEFAULT NULL'],
        ['name' => 'room_number',   'definition' => 'VARCHAR(20) NULL DEFAULT NULL'],
        ['name' => 'is_available',  'definition' => 'TINYINT(1) NOT NULL DEFAULT 1'],
    ];

    $tables['enrollments'] = [
        ['name' => 'paymongo_link_id',  'definition' => 'VARCHAR(100) NULL DEFAULT NULL'],
        ['name' => 'payment_method',    'definition' => 'VARCHAR(50) NULL DEFAULT NULL'],
        ['name' => 'payment_option',    'definition' => 'VARCHAR(50) NULL DEFAULT NULL'],
        ['name' => 'payment_reference', 'definition' => 'VARCHAR(100) NULL DEFAULT NULL'],
        ['name' => 'amount_paid',       'definition' => 'DECIMAL(10,2) NULL DEFAULT 0.00'],
        ['name' => 'cash_amount_requested', 'definition' => 'DECIMAL(10,2) NULL DEFAULT NULL'],
    ];

    foreach ($tables as $table => $columns) {
        $result = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
        $existing = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $existing[] = $row['Field'];
        }

        foreach ($columns as $column) {
            if (!in_array($column['name'], $existing, true)) {
                mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN `{$column['name']}` {$column['definition']}");
            }
        }
    }
}

function seed_course_data($conn)
{
    $courseCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses"))['total'];

    if ((int) $courseCount === 0) {
        $courses = [
            ['course_code' => 'BSIT', 'course_name' => 'Bachelor of Science in Information Technology'],
            ['course_code' => 'BEED', 'course_name' => 'Bachelor of Elementary Education'],
            ['course_code' => 'BSED', 'course_name' => 'Bachelor of Secondary Education'],
        ];

        foreach ($courses as $course) {
            $stmt = mysqli_prepare($conn, "INSERT INTO courses (course_code, course_name) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, 'ss', $course['course_code'], $course['course_name']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    $courses = mysqli_query($conn, "SELECT id, course_code FROM courses ORDER BY id ASC");

    while ($course = mysqli_fetch_assoc($courses)) {
        $sectionCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM sections WHERE course_id = {$course['id']}"))['total'];

        if ((int) $sectionCount === 0) {
            $sectionNames = ['Section A', 'Section B'];
            foreach ($sectionNames as $sectionName) {
                $stmt = mysqli_prepare($conn, "INSERT INTO sections (course_id, section_name, max_slots) VALUES (?, ?, 40)");
                mysqli_stmt_bind_param($stmt, 'is', $course['id'], $sectionName);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        $subjectCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM subjects WHERE course_id = {$course['id']}"))['total'];

        if ((int) $subjectCount === 0) {
            $sections = mysqli_query($conn, "SELECT id FROM sections WHERE course_id = {$course['id']} ORDER BY id ASC");
            $sectionIds = [];
            while ($section = mysqli_fetch_assoc($sections)) {
                $sectionIds[] = $section['id'];
            }

            $subjectTemplates = [
                'BSIT' => [
                    ['code' => 'IT101', 'name' => 'Programming I', 'day' => 'Mon', 'time' => '8:00 AM', 'room' => 'LAB 1'],
                    ['code' => 'IT102', 'name' => 'Database Systems', 'day' => 'Tue', 'time' => '10:00 AM', 'room' => 'LAB 2'],
                    ['code' => 'IT103', 'name' => 'Networking', 'day' => 'Wed', 'time' => '1:00 PM', 'room' => 'LAB 3'],
                    ['code' => 'IT104', 'name' => 'Web Development', 'day' => 'Thu', 'time' => '3:00 PM', 'room' => 'LAB 4'],
                    ['code' => 'IT105', 'name' => 'Systems Analysis', 'day' => 'Fri', 'time' => '9:00 AM', 'room' => 'RM 101'],
                    ['code' => 'IT106', 'name' => 'Ethics in Computing', 'day' => 'Mon', 'time' => '2:00 PM', 'room' => 'RM 102'],
                ],
                'BEED' => [
                    ['code' => 'ED101', 'name' => 'Child Development', 'day' => 'Mon', 'time' => '8:00 AM', 'room' => 'RM 201'],
                    ['code' => 'ED102', 'name' => 'Teaching Methods', 'day' => 'Tue', 'time' => '10:00 AM', 'room' => 'RM 202'],
                    ['code' => 'ED103', 'name' => 'Educational Psychology', 'day' => 'Wed', 'time' => '1:00 PM', 'room' => 'RM 203'],
                    ['code' => 'ED104', 'name' => 'Assessment in Learning', 'day' => 'Thu', 'time' => '3:00 PM', 'room' => 'RM 204'],
                    ['code' => 'ED105', 'name' => 'Reading Instruction', 'day' => 'Fri', 'time' => '9:00 AM', 'room' => 'RM 205'],
                    ['code' => 'ED106', 'name' => 'Curriculum Planning', 'day' => 'Mon', 'time' => '2:00 PM', 'room' => 'RM 206'],
                ],
                'BSED' => [
                    ['code' => 'SED101', 'name' => 'Principles of Teaching', 'day' => 'Mon', 'time' => '8:00 AM', 'room' => 'RM 301'],
                    ['code' => 'SED102', 'name' => 'Educational Research', 'day' => 'Tue', 'time' => '10:00 AM', 'room' => 'RM 302'],
                    ['code' => 'SED103', 'name' => 'Curriculum Development', 'day' => 'Wed', 'time' => '1:00 PM', 'room' => 'RM 303'],
                    ['code' => 'SED104', 'name' => 'Literature Studies', 'day' => 'Thu', 'time' => '3:00 PM', 'room' => 'RM 304'],
                    ['code' => 'SED105', 'name' => 'Classroom Management', 'day' => 'Fri', 'time' => '9:00 AM', 'room' => 'RM 305'],
                    ['code' => 'SED106', 'name' => 'Special Topics', 'day' => 'Mon', 'time' => '2:00 PM', 'room' => 'RM 306'],
                ],
            ];

            $templates = $subjectTemplates[$course['course_code']] ?? $subjectTemplates['BSIT'];
            $index = 0;

            foreach ($templates as $subject) {
                $sectionId = $sectionIds[$index % count($sectionIds)];
                $stmt = mysqli_prepare($conn, "INSERT INTO subjects (course_id, section_id, subject_code, subject_name, units, sem, year_level, schedule_day, schedule_time, room_number) VALUES (?, ?, ?, ?, 3, 1, 1, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'iisssss', $course['id'], $sectionId, $subject['code'], $subject['name'], $subject['day'], $subject['time'], $subject['room']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $index++;
            }
        }
    }
}

function generate_student_number($conn, $course_id)
{
    $year = date('Y');
    $result = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(student_number, 6) AS UNSIGNED)) AS max_num FROM students WHERE student_number IS NOT NULL AND student_number LIKE '{$year}-%'");
    $row = mysqli_fetch_assoc($result);
    $next = ((int) ($row['max_num'] ?? 0)) + 1;

    return $year . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
}
