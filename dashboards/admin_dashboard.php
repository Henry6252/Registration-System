<?php
require_once '../config/db.php';
require_once '../auth/auth.php';

require_role('admin');

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$totalTutors = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'tutor'")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dasboard.css">
    <script>
        function loadContent(page) {
            fetch(`../pages/${page}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('dashboard-content').innerHTML = data;
                })
                .catch(err => console.error("Error loading content:", err));
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#" onclick="loadContent('dashboard_home.php')">Dashboard Home</a></li>
            <li><a href="#" onclick="loadContent('manage users.php')">Manage Users</a></li>
            <li><a href="#" onclick="loadContent('managecourses.php')">Manage Courses</a></li>
            <li><a href="#" onclick="loadContent('departments.php')">Departments</a></li>
            <li><a href="#" onclick="loadContent('website_content.php')">Website Content</a></li>
            <li><a href="#" onclick="loadContent('finance.php')">Finance</a></li>
            <li><a href="#" onclick="loadContent('exams_results.php')">Exams & Results</a></li>
            <li><a href="#" onclick="loadContent('library_system.php')">Library System</a></li>
            <li><a href="#" onclick="loadContent('communication.php')">Communication</a></li>
            <li><a href="#" onclick="loadContent('admissions.php')">Admissions</a></li>
            <li><a href="#" onclick="loadContent('it_support.php')">IT Support</a></li>
            <li><a href="#" onclick="loadContent('institution_settings.php')">Institution Settings</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="navbar">
            <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <a class="logout" href="../auth/logout.php">Logout</a>
        </div>

        <div class="dashboard-content" id="dashboard-content">
            <h2>Welcome to the Admin Dashboard</h2>
            <p>You are logged in with full administrative privileges. Below is a quick overview of the system status.</p>

            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?= $totalUsers ?></p>
                </div>
                <div class="card">
                    <h3>Students</h3>
                    <p><?= $totalStudents ?></p>
                </div>
                <div class="card">
                    <h3>Tutors</h3>
                    <p><?= $totalTutors ?></p>
                </div>
                <div class="card">
                    <h3>Admins</h3>
                    <p><?= $totalAdmins ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
