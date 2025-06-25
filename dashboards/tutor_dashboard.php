<?php 
require_once '../auth/auth.php';
require_once '../config/db.php';

require_role('tutor');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dasboard.css" />
    <style>
        .sidebar ul li a.active {
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Tutor Panel</h2>
        <ul>
            <li><a href="#" data-page="overview" class="active">Dashboard Overview</a></li>
            <li><a href="#" data-page="courses">My Courses</a></li>
            <li><a href="#" data-page="lessons">Create / Update Lessons</a></li>
            <li><a href="#" data-page="assignments">Grade Assignments</a></li>
            <li><a href="#" data-page="performance">Student Performance</a></li>
            <li><a href="#" data-page="messages">Message Students</a></li>
            <li><a href="#" data-page="office_hours">Schedule Office Hours</a></li>
            <li><a href="#" data-page="settings">Settings</a></li>
        </ul>
    </div>

    <div class="navbar">
        <h1>Welcome, Tutor <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

    <div class="dashboard-content" id="dashboardContent">
        <h2>Dashboard Overview</h2>
        <p>This is your personalized tutor dashboard. Use the menu to navigate your responsibilities.</p>
    </div>

    <script>
        const links = document.querySelectorAll('.sidebar ul li a');
        const contentDiv = document.getElementById('dashboardContent');

        links.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();

                // Remove active class from all
                links.forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                const page = link.getAttribute('data-page');

                if (page === 'overview') {
                    contentDiv.innerHTML = `
                        <h2>Dashboard Overview</h2>
                        <p>This is your personalized tutor dashboard. Use the menu to navigate your responsibilities.</p>
                    `;
                } else {
                    fetch(`../pages/${page}.php`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.text();
                        })
                        .then(html => {
                            contentDiv.innerHTML = html;
                        })
                        .catch(err => {
                            contentDiv.innerHTML = '<p style="color:red;">Failed to load content.</p>';
                            console.error(err);
                        });
                }
            });
        });
    </script>

</body>
</html>
