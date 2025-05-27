<?php
require_once 'auth.php';
require_role('tutor');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" href="dasboard.css">
</head>
<body>

    <div class="sidebar">
        <h2>Tutor Panel</h2>
        <ul>
            <li><a href="#">View Students</a></li>
            <li><a href="#">Create Lessons</a></li>
            <li><a href="#">Grade Assignments</a></li>
            <li><a href="#">Message Students</a></li>
        </ul>
    </div>

    <div class="navbar">
        <h1>Welcome, Tutor <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="logout" href="logout.php">Logout</a>
    </div>

    <div class="dashboard-content">
        <p>This is your personalized tutor dashboard.</p>
        <ul class="role-actions">
            <li><a href="#">View Assigned Students</a></li>
            <li><a href="#">Create or Update Lessons</a></li>
            <li><a href="#">Grade Assignments</a></li>
            <li><a href="#">Message Students</a></li>
        </ul>
    </div>

</body>
</html>
