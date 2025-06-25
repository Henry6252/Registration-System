<?php
require_once '../auth/auth.php';
require_role('admin'); // Only admins can reset passwords
require_once '../config/db.php';

$id = $_GET['id'] ?? '';
if (!$id || !is_numeric($id)) {
    die("Invalid or missing user ID.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = trim($_POST['new_password'] ?? '');
    
    if (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            ':password' => $hashed_password,
            ':id' => $id
        ]);

        // Redirect to manage users page after success
        header("Location: ../pages/manage_users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f9f9f9;
        }
        .reset-container {
            background: white;
            padding: 25px;
            max-width: 400px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        label { display: block; margin-bottom: 5px; }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }
        .error { color: red; text-align: center; }
        button {
            padding: 10px 20px;
            width: 100%;
            background: #2ecc71;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #3498db;
        }
    </style>
</head>
<body>
<div class="reset-container">
    <h2>Reset User Password</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="new_password">New Password</label>
        <input type="password" name="new_password" id="new_password" required>

        <button type="submit">Reset Password</button>
        <a href="../pages/manage_users.php">Cancel</a>
    </form>
</div>
</body>
</html>
