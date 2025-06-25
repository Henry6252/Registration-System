<?php
require_once '../auth/auth.php';
require_once '../config/db.php';

require_role('student');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dasboard.css">
    <style>
        .sidebar ul li a.active {
            font-weight: bold;
            color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Student Panel</h2>
        <ul>
            <li><a href="../pages/my_courses.php" class="nav-link">My Courses</a></li>
            <li><a href="../pages/student_sections/grades_feedback.php" class="nav-link">View Grades & Feedback</a></li>
           
             <li><a href="../pages/student_lessons_assignments.php" class="nav-link">Lessons and assignments</a></li>

            <li><a href="../pages/student_sections/exam_dates.php" class="nav-link">Exam Dates</a></li>
            <li><a href="../pages/student_sections/message_tutors.php" class="nav-link">Message Tutors</a></li>
            <li><a href="../pages/student_sections/register_courses.php" class="nav-link">Register for Courses</a></li>
            <li><a href="../pages/student_sections/fee_statements.php" class="nav-link">Fee Statements</a></li>
            <li><a href="../pages/student_sections/library_resources.php" class="nav-link">Library & Resources</a></li>
            <li><a href="../pages/student_sections/career_internship.php" class="nav-link">Career & Internship</a></li>
            <li><a href="../pages/student_sections/support_counseling.php" class="nav-link">Support & Counseling</a></li>
            <li><a href="../pages/student_sections/profile_settings.php" class="nav-link">Profile Settings</a></li>
        </ul>
    </div>

    <div class="navbar">
        <h1>Welcome, Student <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

    <div class="dashboard-content" id="content-area">
        <h2>Dashboard Overview</h2>
        <p>From here, you can manage your academic life, communicate with tutors, and access university services.</p>

        <section class="actions">
            <h3>Quick Actions</h3>
            <ul class="role-actions">
                <li><a href="#" class="nav-link" data-url="../pages/my_courses.php">📚 Access My Courses</a></li>
                <li><a href="#" class="nav-link" data-url="../pages/student_sections/grades_feedback.php">📊 View Grades & Feedback</a></li>
                <li><a href="#" class="nav-link" data-url="../pages/student_lessons_assignments.php">📆 Check Timetable & Exam Dates</a></li>
                <li><a href="#" class="nav-link" data-url="../pages/student_sections/message_tutors.php">💬 Message a Tutor</a></li>
                <li><a href="#" class="nav-link" data-url="../pages/student_sections/fee_statements.php">🧾 View Fee Balance</a></li>
                <li><a href="#" class="nav-link" data-url="../pages/student_sections/career_internship.php">🎓 Explore Internship Opportunities</a></li>
                <li><a href="#" class="nav-link" data-url="../pages/student_sections/profile_settings.php">🔧 Update My Profile</a></li>
            </ul>
        </section>
    </div>

    <script>
        
    document.addEventListener('DOMContentLoaded', function () {
        const links = document.querySelectorAll('.nav-link');

        links.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const url = this.getAttribute('href') || this.dataset.url;
                if (!url) return;

                // Highlight active
                links.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Load content dynamically
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        document.getElementById('content-area').innerHTML = html;
                    })
                    .catch(error => {
                        document.getElementById('content-area').innerHTML = '<p>Error loading content.</p>';
                        console.error('Error loading content:', error);
                    });
            });
        });
    });
    </script>

</body>
</html>
