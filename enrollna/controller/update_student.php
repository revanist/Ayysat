<?php

include "../db/dbconn.php";


$id = $_POST['id'];

$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$last_name = $_POST['last_name'];
$address = $_POST['address'];
$contact = $_POST['contact'];


$sql = "

UPDATE students SET

first_name='$first_name',
middle_name='$middle_name',
last_name='$last_name',
address='$address',
contact='$contact'

WHERE id='$id'

";


$result = mysqli_query($conn, $sql);


if ($result) {

    header("Location: ../admin/students.php");
    exit();
} else {

    echo "Update failed: ";
    echo mysqli_error($conn);
}
