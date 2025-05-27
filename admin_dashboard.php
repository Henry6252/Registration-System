<?php
require_once 'auth.php';
require_role('admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dasboard.css">
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#">Manage Users</a></li>
            <li><a href="#">Assign Roles</a></li>
            <li><a href="#">View Site Reports</a></li>
            <li><a href="#">Handle Registrations</a></li>
        </ul>
    </div>

    <div class="navbar">
        <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="logout" href="logout.php">Logout</a>
    </div>

    <div class="dashboard-content">
        <p>This is your admin dashboard. You can:</p>
        <ul class="role-actions">
            <li><a href="#">Manage Users</a></li>
            <li><a href="#">Assign Roles</a></li>
            <li><a href="#">View Site Reports</a></li>
            <li><a href="#">Handle Registrations</a></li>
        </ul>
    </div>

</body>
</html>
