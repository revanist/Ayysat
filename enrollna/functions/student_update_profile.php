<?php
session_start();
include "../db/dbconn.php";

$id = $_POST['id'];

$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$address = $_POST['address'];

mysqli_query($conn, "
UPDATE students SET
first_name='$first_name',
middle_name='$middle_name',
last_name='$last_name',
email='$email',
contact='$contact',
address='$address'
WHERE id='$id'
");

$_SESSION['fullname'] = $first_name . " " . $last_name;

header("Location: ../student/profile.php");
exit();
