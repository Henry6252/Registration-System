<?php
session_start();
require 'db.php'; 

$success = "";
$error = "";

function getClientIP() {
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $country_code = trim($_POST['country_code']);
    $phone_number = trim($_POST['phone']);
    $phone = $country_code . $phone_number;
    $agreed = isset($_POST['agree']);
    $ip_address = getClientIP();

    $captcha = $_POST['g-recaptcha-response'];
    $secretKey = "6LcplEIrAAAAAFk5FMiS5sIwX6Ho24q10KiaUwwL"; 
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response={$captcha}");
    $captchaSuccess = json_decode($verifyResponse);

    if (!$captchaSuccess->success) {
        $error = "Please verify you're not a robot.";
    } elseif (!$agreed) {
        $error = "You must agree to the Terms and Conditions.";
    } elseif (!preg_match('/^[0-9]{7,15}$/', $phone_number)) {
        $error = "Enter a valid phone number (7–15 digits).";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters long, with letters, numbers, and special characters.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM public.users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);

        if ($stmt->fetch()) {
            $error = "Username or Email already exists.";
        } else {
            try {
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO public.users (first_name, last_name, username, email, phone, password, role)
                                       VALUES (:first_name, :last_name, :username, :email, :phone, :password, :role)");
                $stmt->execute([
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':username' => $username,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':password' => $hashed_password,
                    ':role' => $role,
                    
                ]);

                

                $success = "Registration successful. <a href='login.php'>Now login</a>.";
            } catch (Exception $e) {
                $error = "Database error: " . $e->getMessage();
    echo '<div class="error">' . htmlspecialchars($error) . '</div>';
            }
}

        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .form-group { margin-bottom: 15px; }
        .password-toggle { position: relative; }
        .password-toggle input { padding-right: 40px; }
        .toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .message.success { color: green; margin-bottom: 15px; }
        .message.error { color: red; margin-bottom: 15px; }
        .error-message { color: red; font-size: 0.85rem; margin-top: 4px; }
        #strengthBar { height: 8px; width: 100%; background: #eee; margin-top: 5px; }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="register-page">
<div class="container">
    <h2>Register</h2>

    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" id="registerForm">
        <div class="form-group">
            <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
        </div>
        <div class="form-group">
            <input type="text" name="last_name" placeholder="Last Name" required>
        </div>
        <div class="form-group">
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="tutor">Tutor</option>
                <option value="student">Student</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group phone-group">
            <select name="country_code" required>
                <option value="">Code</option>
                <option value="+1">🇺🇸 +1</option>
                <option value="+44">🇬🇧 +44</option>
                <option value="+254">🇰🇪 +254</option>
                <option value="+91">🇮🇳 +91</option>
                <option value="+61">🇦🇺 +61</option>
                <option value="+27">🇿🇦 +27</option>
            </select>
            <input type="text" name="phone" placeholder="Phone number" required>
        </div>
        <div class="form-group password-toggle">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="toggle-icon" onclick="togglePassword('password', this)">👁️</span>
            <div id="password-error" class="error-message"></div>
            <div id="strengthBar"></div>
        </div>
        <div class="form-group password-toggle">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <span class="toggle-icon" onclick="togglePassword('confirm_password', this)">👁️</span>
            <div id="confirm-password-error" class="error-message"></div>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="agree" required> I agree to the <a href="#">Terms and Conditions</a></label>
        </div>
        <div class="g-recaptcha" data-sitekey="6LcplEIrAAAAABb7qSkkODGHkc7xuDaq5p8DkumB"></div>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    field.type = field.type === "password" ? "text" : "password";
    icon.textContent = field.type === "password" ? "👁️" : "🙈";
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordError = document.getElementById('password-error');
    const confirmPasswordError = document.getElementById('confirm-password-error');

    passwordError.textContent = "";
    confirmPasswordError.textContent = "";

    const passVal = password.value;
    const confirmVal = confirmPassword.value;
    const pattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    let valid = true;

    if (!pattern.test(passVal)) {
        passwordError.textContent = "Password must be 8+ characters, with letters, numbers & special characters.";
        valid = false;
    }

    if (passVal !== confirmVal) {
        confirmPasswordError.textContent = "Passwords do not match.";
        valid = false;
    }

    if (!valid) e.preventDefault();
});

document.getElementById('password').addEventListener('input', function() {
    const val = this.value;
    const bar = document.getElementById('strengthBar');
    let strength = 0;

    if (val.length >= 8) strength++;
    if (val.match(/[A-Z]/)) strength++;
    if (val.match(/[0-9]/)) strength++;
    if (val.match(/[@$!%*?&]/)) strength++;

    const colors = ['#e74c3c', '#f1c40f', '#3498db', '#2ecc71'];
    bar.style.background = colors[strength - 1] || '#eee';
    bar.style.width = (strength / 4) * 100 + '%';
});
</script>
</body>
</html>
