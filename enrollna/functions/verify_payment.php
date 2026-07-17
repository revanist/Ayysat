<?php

session_start();

include "../db/dbconn.php";


$id = $_GET['id'];


$query = "
UPDATE enrollments
SET payment_status='Paid'
WHERE id='$id'
";


if (mysqli_query($conn, $query)) {

    header("Location: ../admin/cashier.php");
    exit();
} else {

    echo "Payment verification failed";
}
