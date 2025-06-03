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
            <li><a href="#">Dashboard Home</a></li>
            <li><a href="#">Manage Users</a></li>
            <li><a href="#">Manage Courses</a></li>
            <li><a href="#">Departments</a></li>
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
            <h2>Full Access Admin Dashboard</h2>
            <p>You have access to all administrative functionalities:</p>

            <div class="role-section">
                <h3>1. User Management</h3>
                <ul>
                    <li><a href="#">Manage Users (students, tutors, admins)</a></li>
                    <li><a href="#">Reset Passwords / Assign Roles</a></li>
                    <li><a href="#">Approve/Deactivate Accounts</a></li>
                </ul>
            </div>

            <div class="role-section">
                <h3>2. Course & Department Management</h3>
                <ul>
                    <li><a href="#">Create/Edit/Delete Courses</a></li>
                    <li><a href="#">Assign Courses to Tutors/Students</a></li>
                    <li><a href="#">Manage Departments</a></li>
                </ul>
            </div>

            <div class="role-section">
                <h3>3. Content Management</h3>
                <ul>
                    <li><a href="#">Update Website Content (News, Events)</a></li>
                    <li><a href="#">Manage Academic Calendar</a></li>
                </ul>
            </div>

            <div class="role-section">
                <h3>4. Finance Management</h3>
                <ul>
                    <li><a href="#">View/Edit Student Payments</a></li>
                    <li><a href="#">Generate Invoices & Reports</a></li>
                </ul>
            </div>

            <div class="role-section">
                <h3>5. Exams & Results</h3>
                <ul>
                    <li><a href="#">Upload Exam Results</a></li>
                    <li><a href="#">Generate Transcripts</a></li>
                </ul>
            </div>

            <div class="role-section">
                <h3>6. Library Management</h3>
                <ul>
                    <li><a href="#">Manage Digital & Physical Resources</a></li>
                    <li><a href="#">Track Borrowing & Returns</a></li>
                </ul>
            </div>

            <div class="role-section">
                <h3>7. Communication</h3>
                <ul>
                    <li><a href="#">Send Bulk Emails / SMS</a></li>
                    <li><a href="#">Dashboard Notifications</a></li>
                </ul>
            </div>

            <div class="role-section">
                <h3>8. Admissions</h3>
                <ul>
                    <li><a href="#">Review Applications</a></li>
                    <li><a href="#">Manage Entrance Exams & Forms</a></li>
                </ul>
            </div>

            <div class="role-section">
                <h3>9. IT & Settings</h3>
                <ul>
                    <li><a href="#">Provide Technical Support</a></li>
                    <li><a href="#">Edit Institution Settings</a></li>
                    <li><a href="#">View Logs & Backups</a></li>
                </ul>
            </div>

        </div>
    </div>

</body>
</html>
