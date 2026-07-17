<?php

session_start();

require "../db/dbconn.php";


// Check if student is logged in

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $user_id = $_SESSION['user_id'];


    // PERSONAL INFORMATION

    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $sex = $_POST['sex'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];


    // ACADEMIC INFORMATION

    $course_id = $_POST['course_id'];
    $year_level = $_POST['year_level'];
    $semester = $_POST['semester'];
    $school_year = $_POST['school_year'];


    /*
    ------------------------------------
    SAVE STUDENT INFORMATION
    ------------------------------------
    */


    $studentQuery = mysqli_query($conn, "

        INSERT INTO students
        (
        user_id,
        first_name,
        middle_name,
        last_name,
        birthdate,
        sex,
        address,
        contact,
        email,
        course_id,
        year_level
        )

        VALUES

        (
        '$user_id',
        '$first_name',
        '$middle_name',
        '$last_name',
        '$birthdate',
        '$sex',
        '$address',
        '$contact',
        '$email',
        '$course_id',
        '$year_level'
        )

    ");



    if (!$studentQuery) {

        die("Student Save Error: " . mysqli_error($conn));
    }



    // Get student ID

    $student_id = mysqli_insert_id($conn);



    /*
    ------------------------------------
    CREATE ENROLLMENT
    ------------------------------------

    Default status is Pending.
    Admin will update this later.

    */


    $enrollmentQuery = mysqli_query($conn, "

        INSERT INTO enrollments
        (
        student_id,
        school_year,
        sem,
        status,
        payment_status
        )

        VALUES

        (
        '$student_id',
        '$school_year',
        '$semester',
        'Pending',
        'Pending'
        )

    ");



    if (!$enrollmentQuery) {

        die("Enrollment Save Error: " . mysqli_error($conn));
    }



    $_SESSION['success'] = "Application submitted successfully.";


    header("Location: ../student/student_dashboard.php");

    exit();
}
