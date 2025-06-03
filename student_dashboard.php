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
            <li><a href="#">My Courses</a></li>
            <li><a href="#">Course Materials</a></li>
            <li><a href="#">Submit Assignments</a></li>
            <li><a href="#">View Grades & Feedback</a></li>
            <li><a href="#">Class Schedule</a></li>
            <li><a href="#">Exam Dates</a></li>
            <li><a href="#">Message Tutors</a></li>
            <li><a href="#">Register for Courses</a></li>
            <li><a href="#">Fee Statements</a></li>
            <li><a href="#">Library & Resources</a></li>
            <li><a href="#">Career & Internship</a></li>
            <li><a href="#">Support & Counseling</a></li>
            <li><a href="#">Profile Settings</a></li>
        </ul>
    </div>

    <div class="navbar">
        <h1>Welcome, Student <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="logout" href="logout.php">Logout</a>
    </div>

    <div class="dashboard-content">
        <h2>Dashboard Overview</h2>
        <p>From here, you can manage your academic life, communicate with tutors, and access university services.</p>

        <section class="actions">
            <h3>Quick Actions</h3>
            <ul class="role-actions">
                <li><a href="#">📚 Access My Courses</a></li>
                <li><a href="#">📥 Submit Assignments</a></li>
                <li><a href="#">📊 View Grades & Feedback</a></li>
                <li><a href="#">📆 Check Timetable & Exam Dates</a></li>
                <li><a href="#">💬 Message a Tutor</a></li>
                <li><a href="#">🧾 View Fee Balance</a></li>
                <li><a href="#">🎓 Explore Internship Opportunities</a></li>
                <li><a href="#">🔧 Update My Profile</a></li>
            </ul>
        </section>
    </div>

</body>
</html>
