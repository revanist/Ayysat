<?php
session_start();
require "../db/dbconn.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AYYSAT Admission Portal</title>

    <link href="../css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/enrollment.css">
</head>

<body>

    <div class="header">
        <h2>AYYSAT College Admission Portal</h2>
        <p>College Application Form</p>
    </div>

    <div class="container my-5">

        <div class="card">

            <div class="card-body p-4">

                <form action="../functions/process_application.php" method="POST">

                    <!-- PERSONAL INFORMATION -->

                    <h4 class="section-title">Personal Information</h4>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="birthdate" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sex</label>
                            <select class="form-select" name="sex" required>
                                <option value="">Select Sex</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Civil Status</label>
                            <select class="form-select" name="civil_status">
                                <option value="">Select</option>
                                <option>Single</option>
                                <option>Married</option>
                                <option>Widowed</option>
                                <option>Separated</option>
                            </select>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nationality</label>
                            <input type="text" class="form-control" name="nationality" placeholder="Filipino">
                        </div>

                    </div>

                    <hr>

                    <!-- CONTACT INFORMATION -->

                    <h4 class="section-title">Contact Information</h4>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" name="contact" maxlength="11"
                                placeholder="09XXXXXXXXX" required>
                        </div>

                    </div>

                    <div class="mb-4">
                        <label class="form-label">Home Address</label>
                        <textarea class="form-control" rows="3" name="address" required></textarea>
                    </div>

                    <hr>

                    <!-- ACADEMIC INFORMATION -->

                    <h4 class="section-title">Academic Information</h4>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last School Attended</label>
                            <input type="text" class="form-control" name="last_school" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">School Address</label>
                            <input type="text" class="form-control" name="school_address">
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Graduation Year</label>
                            <input type="number" class="form-control" name="graduation_year" min="2000" max="2035"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Senior High School Strand</label>

                            <select class="form-select" name="strand" required>

                                <option value="">Select Strand</option>

                                <option>STEM</option>
                                <option>ABM</option>
                                <option>HUMSS</option>
                                <option>GAS</option>
                                <option>TVL</option>
                                <option>ICT</option>
                                <option>Arts & Design</option>
                                <option>Sports</option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">General Average (GWA)</label>
                            <input type="number" class="form-control" name="gwa" min="75" max="100" step="0.01"
                                placeholder="Ex. 91.25">
                        </div>

                    </div>

                    <hr>

                    <!-- PROGRAM INFORMATION -->

                    <h4 class="section-title">Program Information</h4>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Enrollment Type</label>

                            <select class="form-select" name="enrollment_type" required>
                                <option value="">Select Type</option>
                                <option>New Student</option>
                                <option>Transferee</option>
                                <option>Returning Student</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course / Program</label>

                            <?php
                            $courses = mysqli_query($conn, "SELECT id, course_code, course_name FROM courses ORDER BY course_code");
                            ?>

                            <select class="form-select" name="course_id" required>

                                <option value="">Select Course</option>

                                <?php while ($course = mysqli_fetch_assoc($courses)) { ?>

                                    <option value="<?= $course['id']; ?>">
                                        <?= htmlspecialchars($course['course_code'] . " - " . $course['course_name']); ?>
                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Year Level</label>

                            <select class="form-select" name="year_level" required>

                                <option value="">Select Year</option>

                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Semester</label>

                            <select class="form-select" name="semester" required>

                                <option value="">Select Semester</option>

                                <option value="1">1st Semester</option>
                                <option value="2">2nd Semester</option>
                                <option value="3">Summer</option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">School Year</label>

                            <input type="text" class="form-control" name="school_year" value="2026-2027" readonly>

                        </div>

                    </div>

                    <div class="text-center mt-4">

                        <button type="submit" class="btn btn-ayysat btn-lg px-5">
                            Enroll Now
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <script src="../js/bootstrap.bundle.js"></script>

</body>

</html>