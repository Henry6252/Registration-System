<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

$id = $_GET['id'] ?? '';
if (!$id) die("User ID missing");

$sql = "SELECT status FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$new_status = $user['status'] === 'active' ? 'inactive' : 'active';

$sql = "UPDATE users SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $id);
$stmt->execute();

header("Location: manage_users.php");
exit;
