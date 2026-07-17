<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin_login.php");
    exit();
}

include "../db/dbconn.php";

$id = intval($_GET['id']);

$query = mysqli_query($conn, "
    SELECT
        e.*,
        s.student_number,
        s.first_name,
        s.middle_name,
        s.last_name,
        s.sex,
        s.birthdate,
        s.address,
        s.contact,
        s.email,
        s.guardian,
        s.guardian_contact,
        c.course_name
    FROM enrollments e
    LEFT JOIN students s ON e.student_id = s.id
    LEFT JOIN courses c ON s.course_id = c.id
    WHERE e.id = $id
    ");

$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Enrollment Details</title>
</head>

<body>

    <h2>Enrollment Details</h2>

    <p><strong>Student Number:</strong> <?php echo $data['student_number']; ?></p>

    <p><strong>Name:</strong>
        <?php
        echo $data['first_name'] . " " .
            $data['middle_name'] . " " .
            $data['last_name'];
        ?>
    </p>

    <p><strong>Course:</strong> <?php echo $data['course_name']; ?></p>
    <p><strong>Sex:</strong> <?php echo $data['sex']; ?></p>
    <p><strong>Birthdate:</strong> <?php echo $data['birthdate']; ?></p>
    <p><strong>Address:</strong> <?php echo $data['address']; ?></p>
    <p><strong>Contact:</strong> <?php echo $data['contact']; ?></p>
    <p><strong>Email:</strong> <?php echo $data['email']; ?></p>
    <p><strong>Guardian:</strong> <?php echo $data['guardian']; ?></p>
    <p><strong>Guardian Contact:</strong> <?php echo $data['guardian_contact']; ?></p>
    <p><strong>Status:</strong> <?php echo $data['STATUS']; ?></p>

    <a href="../admin/enrollments.php">Back</a>
</body>

</html>