
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/auth.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>

<body>

    <div id="app">
        <div class="container">
            <div class="card">
                <div class="logo">
                    <a href="../webhome.php">
                        <img src="../img/eyysat.png" alt="AYYSAT Logo">
                    </a>
                    <h1>EYYSAT</h1>
                    <p>Administrator Registration</p>
                </div>

                <form action="../functions/registration_process.php" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" v-model="fullname" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" v-model="email" placeholder="admin@eyysat.edu" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input :type="showPassword ? 'text' : 'password'" name="password" v-model="password" placeholder="Create a password" required>
                        <small v-if="password.length > 0" class="password-hint" :style="{ color: passwordStrength.color }">{{ passwordStrength.text }}</small>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input :type="showPassword ? 'text' : 'password'" name="confirm_password" v-model="confirmPassword" placeholder="Re-enter password" required>
                    </div>

                    <div class="form-group checkbox-row">
                        <input type="checkbox" v-model="showPassword">
                        <label class="checkbox-label">Show Password</label>
                    </div>

                    <button class="btn" type="submit" :disabled="!formValid">
                        Register Administrator
                    </button>
                </form>

                <div class="link">
                    Already have an account?
                    <a href="admin_login.php">Login Here</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    fullname: '',
                    email: '',
                    password: '',
                    confirmPassword: '',
                    showPassword: false
                };
            },
            computed: {
                passwordStrength() {
                    if (this.password.length < 6) {
                        return {
                            text: 'Weak password',
                            color: '#d9480f'
                        };
                    }
                    if (this.password.length < 10) {
                        return {
                            text: 'Medium password',
                            color: '#f59f00'
                        };
                    }
                    return {
                        text: 'Strong password',
                        color: '#2f9e44'
                    };
                },
                formValid() {
                    return this.fullname.trim().length > 0 && this.email.trim().length > 0 && this.password.length >= 6 && this.password === this.confirmPassword;
                }
            }
        }).mount('#app');
    </script>

</body>

</html>