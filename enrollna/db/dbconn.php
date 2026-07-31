<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "enrollna";

$conn = new mysqli($servername, $username, $password, $database);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
