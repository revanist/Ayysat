<?php

session_start();

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../auth/admin_login.php");
    exit();
}

include "../db/dbconn.php";


if (

    !isset($_GET['id']) ||

    !isset($_GET['status'])

) {

    die("Invalid request.");
}


$id = (int)$_GET['id'];

$status = strtolower($_GET['status']);


if (

    $status != "approved" &&

    $status != "rejected"

) {

    die("Invalid status.");
}


$stmt = mysqli_prepare(

    $conn,

    "UPDATE enrollments
SET STATUS=?
WHERE id=?"

);


mysqli_stmt_bind_param(

    $stmt,

    "si",

    $status,

    $id

);


if (mysqli_stmt_execute($stmt)) {

    header("Location: ../admin/enrollments.php");

    exit();
} else {

    echo mysqli_error($conn);
}
