<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Registration</title>

    <link rel="stylesheet" href="../css/style.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

</head>

<body>

    <div id="app">
        <div class="container">
            <div class="card">
                <div class="logo">
                    <h1>🎓 Eyysat</h1>
                    <p>Administrator Registration</p>
                </div>

                <form action="../functions/registration_process.php" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" v-model="fullname" required>

                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" v-model="email" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input :type="showPassword ? 'text' : 'password'" name="password" v-model="password" required>

                        <small :style="{color: passwordStrength.color}">
                            {{ passwordStrength.text }}
                        </small>

                        <label>Confirm Password</label>
                        <input :type="showPassword ? 'text' : 'password'" name="confirm_password" v-model="confirmPassword" required>
                    </div>

                    <div class="form-group">
                        <input type="checkbox" v-model="showPassword">
                        Show Password
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

                }

            },

            computed: {

                passwordStrength() {

                    if (this.password.length < 6) {

                        return {
                            text: 'Weak Password',
                            color: 'red'
                        }

                    }

                    if (this.password.length < 10) {

                        return {
                            text: 'Medium Password',
                            color: 'orange'
                        }

                    }

                    return {
                        text: 'Strong Password',
                        color: 'green'
                    }

                },

                formValid() {

                    return (
                        this.password.length >= 6 &&
                        this.password === this.confirmPassword
                    )

                }

            }

        }).mount('#app');
    </script>

</body>

</html>