<?php
require_once 'auth.php';
require_role('admin');
require 'db.php';

// Fetch summary data for dashboard home
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$totalTutors = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'tutor'")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

// Determine which page content to load inside dashboard-content
$page = $_GET['page'] ?? 'home';

// Simple whitelist to avoid arbitrary file inclusion
$allowedPages = ['home', 'manage_users', 'manage_courses', 'departments'];
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/dashboard.css" />
    <style>
        /* Add or override styles here if needed */
        .dashboard-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            flex: 1 1 200px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            margin-bottom: 10px;
            color: #34495e;
        }
        .dashboard-overview,
        .quick-tips {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .dashboard-overview h3,
        .quick-tips h3 {
            margin-bottom: 15px;
            color: #2980b9;
        }
        .dashboard-overview ul,
        .quick-tips ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        .dashboard-overview ul li,
        .quick-tips ul li {
            margin-bottom: 8px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php?page=home" <?php if($page === 'home') echo 'class="active"'; ?>>Dashboard Home</a></li>
            <li><a href="admin_dashboard.php?page=manage_users" <?php if($page === 'manage_users') echo 'class="active"'; ?>>Manage Users</a></li>
            <li><a href="admin_dashboard.php?page=manage_courses" <?php if($page === 'manage_courses') echo 'class="active"'; ?>>Manage Courses</a></li>
            <li><a href="admin_dashboard.php?page=departments" <?php if($page === 'departments') echo 'class="active"'; ?>>Departments</a></li>
            <li><a href="#">Website Content</a></li>
            <li><a href="#">Finance</a></li>
            <li><a href="#">Exams & Results</a></li>
            <li><a href="#">Library System</a></li>
            <li><a href="#">Communication</a></li>
            <li><a href="#">Admissions</a></li>
            <li><a href="#">IT Support</a></li>
            <li><a href="#">Institution Settings</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="navbar">
            <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <a class="logout" href="logout.php">Logout</a>
        </div>

        <div class="dashboard-content">
            <?php
                if ($page === 'home'):
            ?>
                <h2>Welcome to the Admin Dashboard</h2>
                <p>Manage your institution efficiently with quick access to key system statistics and important updates.</p>

                <div class="dashboard-cards">
                    <div class="card">
                        <h3>Total Users</h3>
                        <p><?php echo $totalUsers; ?></p>
                    </div>
                    <div class="card">
                        <h3>Students</h3>
                        <p><?php echo $totalStudents; ?></p>
                    </div>
                    <div class="card">
                        <h3>Tutors</h3>
                        <p><?php echo $totalTutors; ?></p>
                    </div>
                    <div class="card">
                        <h3>Admins</h3>
                        <p><?php echo $totalAdmins; ?></p>
                    </div>
                </div>

                <section class="dashboard-overview">
                    <h3>System Overview</h3>
                    <ul>
                        <li><strong>User Management:</strong> Add, edit, or remove users including students, tutors, and other admins.</li>
                        <li><strong>Course & Department Management:</strong> Create courses, assign tutors, and organize departments.</li>
                        <li><strong>Academic Semesters:</strong> Define semesters and manage course scheduling.</li>
                        <li><strong>Exams & Results:</strong> Upload results and generate transcripts efficiently.</li>
                        <li><strong>Financials:</strong> Track student payments, generate invoices, and run financial reports.</li>
                        <li><strong>Communication:</strong> Send notifications and manage bulk emails or SMS.</li>
                    </ul>
                </section>

                <section class="quick-tips">
                    <h3>Quick Tips</h3>
                    <ul>
                        <li>Use the sidebar navigation to access specific modules.</li>
                        <li>Ensure all tutors are assigned to their respective courses before semester start.</li>
                        <li>Regularly update the academic calendar and institution settings.</li>
                        <li>Keep backup of important data via the IT Support section.</li>
                    </ul>
                </section>

            <?php elseif ($page === 'manage_users'): ?>
                <?php include 'manage_users.php'; ?>

            <?php elseif ($page === 'manage_courses'): ?>
                <?php include 'manage_courses.php'; ?>

            <?php elseif ($page === 'departments'): ?>
                <?php include 'departments.php'; ?>

            <?php else: ?>
                <p>Page not found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
