<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

$id = $_GET['id'] ?? '';
if (!$id) die("User ID missing");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $role, $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

$sql = "SELECT username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="users.css">

</head>
<body>
<h2>Edit User</h2>
<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>
    
    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>
    
    <label>Role:</label><br>
    <select name="role">
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="tutor" <?= $user['role'] == 'tutor' ? 'selected' : '' ?>>Tutor</option>
        <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>Student</option>
    </select><br><br>

    <button type="submit">Save Changes</button>
    <a href="manage_users.php">Cancel</a>
</form>
</body>
</html>
