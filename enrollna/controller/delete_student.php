<?php

include "../db/dbconn.php";


if (isset($_GET['id'])) {


    $id = $_GET['id'];


    // delete enrollment first
    mysqli_query($conn, "
    DELETE FROM enrollments 
    WHERE student_id='$id'
    ");


    // delete student
    $delete = mysqli_query($conn, "
    DELETE FROM students 
    WHERE id='$id'
    ");


    if ($delete) {

        header("Location: ../admin/students.php");
        exit();
    } else {

        echo "Delete failed: " . mysqli_error($conn);
    }
} else {

    echo "No ID received";
}
