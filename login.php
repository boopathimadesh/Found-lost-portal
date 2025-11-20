<?php
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $result = loginUser($email, $password);
        if ($result === true) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result;
        }
    } elseif (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            $result = registerUser($name, $email, $password);
            if ($result === true) {
                $success = "Registration successful. Please login.";
            } else {
                $error = $result;
            }
        }
    }
}

$pageTitle = "Login";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KEC Campus Finder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-container">
    <div class="login-box">
        <div class="login-tabs">
            <div class="login-tab active" id="login-tab">Login</div>
            <div class="login-tab" id="register-tab">Register</div>
        </div>
        
        <div class="login-content">
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form id="login-form" method="POST" action="">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your @kongu.edu email" required>
                </div>
                <div class="form-group">
                    <label for="password" class="required">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            
            <!-- Registration Form -->
            <form id="register-form" method="POST" action="" style="display: none;">
                <input type="hidden" name="register" value="1">
                <div class="form-group">
                    <label for="name" class="required">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="reg-email" class="required">Email</label>
                    <input type="email" id="reg-email" name="email" placeholder="Enter your @kongu.edu email" required>
                </div>
                <div class="form-group">
                    <label for="reg-password" class="required">Password</label>
                    <input type="password" id="reg-password" name="password" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password" class="required">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            
            <div class="form-footer">
                <p>KEC Campus Finder - Lost & Found System</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginTab = document.getElementById('login-tab');
            const registerTab = document.getElementById('register-tab');
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            
            loginTab.addEventListener('click', function() {
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            });
            
            registerTab.addEventListener('click', function() {
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                registerForm.style.display = 'block';
                loginForm.style.display = 'none';
            });
            
            // Validate email domain on registration
            document.getElementById('register-form').addEventListener('submit', function(e) {
                const email = document.getElementById('reg-email').value;
                if (!email.endsWith('@kongu.edu')) {
                    e.preventDefault();
                    alert('Only @kongu.edu emails are allowed for registration.');
                }
            });
        });
    </script>
</body>
</html>