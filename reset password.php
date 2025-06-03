<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

$id = $_GET['id'] ?? '';
if (!$id) die("User ID missing");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_password, $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="users.css">

</head>
<body>
<h2>Reset Password</h2>
<form method="post">
    <label>New Password:</label><br>
    <input type="password" name="new_password" required><br><br>
    <button type="submit">Reset Password</button>
    <a href="manage_users.php">Cancel</a>
</form>
</body>
</html>
