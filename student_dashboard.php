<?php
require_once 'auth.php';
require_role('student');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="dasboard.css">
</head>
<body>

    <div class="sidebar">
        <h2>Student Panel</h2>
        <ul>
            <li><a href="#">View Lessons</a></li>
            <li><a href="#">Submit Assignments</a></li>
            <li><a href="#">Track Grades</a></li>
            <li><a href="#">Message Tutors</a></li>
        </ul>
    </div>

    <div class="navbar">
        <h1>Welcome, Student <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="logout" href="logout.php">Logout</a>
    </div>

    <div class="dashboard-content">
        <p>This is your student dashboard. From here, you can:</p>
        <ul class="role-actions">
            <li><a href="#">View Lessons</a></li>
            <li><a href="#">Submit Assignments</a></li>
            <li><a href="#">Track Your Grades</a></li>
            <li><a href="#">Message Tutors</a></li>
        </ul>
    </div>

</body>
</html>
