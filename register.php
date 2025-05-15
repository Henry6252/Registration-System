<?php
session_start();

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['country_code']) . trim($_POST['phone']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $users = json_decode(file_get_contents('users.json'), true) ?? [];

        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $error = "Username already exists.";
                break;
            }
        }

        if (!$error) {
            $users[] = [
                'username' => $username,
                'password' => $hashed_password
            ];
            file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
            $success = "Registration successful.";
            echo "<script>
                setTimeout(function() {
                    var code = prompt('Enter the verification code sent to your phone ($phone):');
                    if (code === null || code === '') {
                        alert('Verification cancelled.');
                    } else {
                        alert('Account verified successfully!');
                        window.location.href = 'login.php';
                    }
                }, 500);
            </script>";
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
</head>
<body class="register-page">

<div class="container">
    <h2>Register</h2>

    <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <!-- First Name and Last Name -->
        <div class="name-group">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
        </div>

        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>

        <!-- Phone Number and Country Code -->
        <div class="phone-group">
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

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<!-- JavaScript Password Validation -->
<script>
document.querySelector("form").addEventListener("submit", function(e) {
    const password = document.querySelector('input[name="password"]').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!passwordPattern.test(password)) {
        alert("Password must be at least 8 characters long, contain letters, numbers, and at least one special character.");
        e.preventDefault();
    } else if (password !== confirmPassword) {
        alert("Passwords do not match.");
        e.preventDefault();
    }
});
</script>

</body>
</html>
