<?php
require_once 'auth.php';
require_role('admin');

// Assume you already have a DB connection in $conn
require_once 'db.php';

$search = $_GET['search'] ?? '';
$sql = "SELECT id, username, email, role, status FROM users WHERE username LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($sql);
$like = "%$search%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .actions button {
            margin-right: 5px;
        }
        .search-form {
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="main">
    <h1>Manage Users</h1>

    <form class="search-form" method="get">
        <input type="text" name="search" placeholder="Search by username or email" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars($user['status']) ?></td>
                <td class="actions">
                    <button onclick="location.href='edit_user.php?id=<?= $user['id'] ?>'">Edit</button>
                    <button onclick="location.href='reset_password.php?id=<?= $user['id'] ?>'">Reset Password</button>
                    <button onclick="location.href='toggle_user.php?id=<?= $user['id'] ?>'">
                        <?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
