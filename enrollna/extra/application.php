<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment Form</title>
    <link rel="stylesheet" href="css/application.css">
    <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body style="background-color: #0d3b66; min-height: 100vh;">

    <div class="hero-bubble-large"></div>
    <div class="hero-bubble-small"></div>

    <div class="form-container">
        <h2>Student Enrollment Form</h2>
        <p class="subtitle">Please fill out the form carefully to register.</p>

        <form action="#" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <div class="radio-group">
                        <label class="radio-label"><input type="radio" name="gender" value="male" required> Male</label>
                        <label class="radio-label"><input type="radio" name="gender" value="female"> Female</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="course">Course / Program</label>
                <select id="course" name="course" required>
                    <option value="" disabled selected>Select a course</option>
                    <option value="cs">Computer Science</option>
                    <option value="it">Information Technology</option>
                    <option value="ba">Business Administration</option>
                    <option value="eng">Engineering</option>
                </select>
            </div>

            <div class="form-group">
                <label for="address">Home Address</label>
                <input type="text" id="address" name="address" required>
            </div>

            <button type="submit">Submit Enrollment</button>
        </form>
    </div>

    <script src="js/bootstrap.bundle.js"></script>

</body>

</html>