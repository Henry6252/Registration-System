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
            <li><a href="#">Dashboard Overview</a></li>
            <li><a href="#">My Courses</a></li>
            <li><a href="#">Create / Update Lessons</a></li>
            <li><a href="#">Grade Assignments</a></li>
            <li><a href="#">Student Performance</a></li>
            <li><a href="#">Message Students</a></li>
            <li><a href="#">Schedule Office Hours</a></li>
            <li><a href="#">Settings</a></li>
        </ul>
    </div>

    <div class="navbar">
        <h1>Welcome, Tutor <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="logout" href="logout.php">Logout</a>
    </div>

    <div class="dashboard-content">
        <h2>Dashboard Overview</h2>
        <p>This is your personalized tutor dashboard. Use the menu to navigate your responsibilities.</p>

        <section class="actions">
            <h3>Quick Actions</h3>
            <ul class="role-actions">
                <li><a href="#">📘 View or Manage Your Courses</a></li>
                <li><a href="#">📝 Create or Edit Lesson Materials</a></li>
                <li><a href="#">✅ Grade Submitted Work</a></li>
                <li><a href="#">📊 View Student Performance</a></li>
                <li><a href="#">💬 Communicate with Students</a></li>
                <li><a href="#">🕒 Schedule Office Hours</a></li>
            </ul>
        </section>
    </div>

</body>
</html>
