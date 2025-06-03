<?php
session_start();
require 'db.php';

$success = "";
$error = "";

if (isset($_GET['registered']) && $_GET['registered'] === 'true') {
    $success = "Registration successful! <a href='login.php'>Click here to login</a>.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    // $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];

        switch ($user['role']) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'tutor':
                header("Location: tutor_dashboard.php");
                break;
            case 'student':
                header("Location: student_dashboard.php");
                break;
            default:
                $error = "Unknown user role.";
                break;
        }
        exit();
    } else {
        $error = "Invalid username, password, or role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="container">
        <h2>Login</h2>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="username" placeholder="Username or Email" required>

            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

            <a href="forgot_password.php" class="link">Forgot your password?</a>

           

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        function togglePassword() {
            const passField = document.getElementById("password");
            passField.type = passField.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>