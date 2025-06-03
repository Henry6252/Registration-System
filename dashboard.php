<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">

</head>
<body class="dashboard-page">



    
    <div class="sidebar" id="sidebar">
        <a href="#" class="nav-link active" onclick="showSection('dashboard')">Dashboard</a>
        <a href="#" class="nav-link" onclick="showSection('profile')">Profile</a>
        <a href="#" class="nav-link" onclick="showSection('messages')">Messages</a>
        <a href="#" class="nav-link" onclick="showSection('settings')">Settings</a>
    </div>

    
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <span class="menu-toggle" onclick="toggleSidebar()">☰</span>
            <h1 style="margin-left: 10px;">My Dashboard</h1>
        </div>
        <div class="right">
            <a href="logout.php">Logout</a>
            <button class="toggle-dark" onclick="toggleDarkMode()">🌓</button>
        </div>
    </div>

    
    <div class="content">
        <div id="dashboard" class="card">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>This is your dashboard. Use the sidebar to explore other sections.</p>
        </div>

        <div id="profile" class="card" style="display: none;">
            <h2>Profile</h2>
            <p>Name: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <p>Email: example@email.com</p>
            <p>Account Created: Jan 2024</p>
        </div>

        <div id="messages" class="card" style="display: none;">
            <h2>Messages</h2>
            <p>You have no new messages.</p>
        </div>

        <div id="settings" class="card" style="display: none;">
            <h2>Settings</h2>
            <p>Adjust your preferences, account details, and display options here.</p>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("show");
        }

        function showSection(id) {
            const sections = ['dashboard', 'profile', 'messages', 'settings'];
            sections.forEach(section => {
                document.getElementById(section).style.display = section === id ? 'block' : 'none';
            });

            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        }

    
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
        }
    </script>

</body>
</html>
