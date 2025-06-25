<?php
require_once '../auth/auth.php';
require_role('admin'); // Ensure only admin can toggle user status
require_once '../config/db.php';

$id = $_GET['id'] ?? '';
if (!$id || !is_numeric($id)) {
    die("Invalid or missing user ID.");
}

// Fetch current user status
$stmt = $pdo->prepare("SELECT status FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Toggle status
$new_status = ($user['status'] === 'active') ? 'inactive' : 'active';

$stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
$stmt->execute([
    ':status' => $new_status,
    ':id' => $id
]);

header("Location: ../pages/manage_users.php");
exit;
