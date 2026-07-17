<?php

include "../db/dbconn.php";


$id = $_GET['id'];


$query = mysqli_query($conn, "
SELECT * FROM students 
WHERE id='$id'
");


$student = mysqli_fetch_assoc($query);


?>


<!DOCTYPE html>
<html>

<head>

    <title>Edit Student</title>

    <link rel="stylesheet" href="../css/bootstrap.css">

</head>


<body>


    <div class="container mt-5">


        <h2>Edit Student</h2>


        <form action="../functions/update_student.php" method="POST">


            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">



            <label>First Name</label>

            <input type="text" class="form-control" name="first_name" value="<?php echo $student['first_name']; ?>">



            <label>Middle Name</label>

            <input type="text" class="form-control" name="middle_name" value="<?php echo $student['middle_name']; ?>">



            <label>Last Name</label>

            <input type="text" class="form-control" name="last_name" value="<?php echo $student['last_name']; ?>">



            <label>Address</label>

            <textarea class="form-control" name="address">

<?php echo $student['address']; ?>

</textarea>



            <label>Contact</label>

            <input type="text" class="form-control" name="contact" value="<?php echo $student['contact']; ?>">



            <br>


            <button class="btn btn-primary">

                Update Student

            </button>


        </form>


    </div>


</body>

</html>