<?php
require_once '../auth/auth.php';
require_role('admin');
require_once '../config/db.php';

$errors = [];
$success = '';
$departments = [];
$semesters = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_department'])) {
        $dept_name = trim($_POST['department_name'] ?? '');
        if (!$dept_name) {
            $errors[] = "Department name cannot be empty.";
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM departments WHERE name = :name");
            $stmt->execute(['name' => $dept_name]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Department '$dept_name' already exists.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO departments (name) VALUES (:name)");
                $stmt->execute(['name' => $dept_name]);
                $success = "Department '$dept_name' created successfully!";
            }
        }
    }

    if (isset($_POST['create_semester'])) {
        $semester_name = trim($_POST['semester_name'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';

        if (!$semester_name) $errors[] = "Semester name is required.";
        if (!$start_date) $errors[] = "Start date is required.";
        if (!$end_date) $errors[] = "End date is required.";

        if (!$errors) {
            $stmt = $pdo->prepare("INSERT INTO semesters (name, start_date, end_date) VALUES (:name, :start_date, :end_date)");
            $stmt->execute([
                'name' => $semester_name,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            $success = "Semester '$semester_name' created successfully!";
        }
    }
}

$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$semesters = $pdo->query("SELECT id, name, start_date, end_date FROM semesters ORDER BY start_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Departments & Semesters</title>
    <link rel="stylesheet" href="../assets/css/dasboard.css" />
    <link rel="stylesheet" href="../assets/css/global.css" />
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="../dashboards/admin_dashboard.php">Dashboard Home</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="managecourses.php">Manage Courses</a></li>
        <li><a href="departments.php">Departments</a></li>
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
        <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

    <div class="dashboard-content">
        <h2>Manage Departments & Semesters</h2>

        <div class="messages">
            <?php foreach ($errors as $error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
            <?php if ($success): ?>
                <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
        </div>

        <form method="POST">
            <h3>Create New Department</h3>
            <label for="department_name">Department Name:</label>
            <input type="text" id="department_name" name="department_name" required />
            <button type="submit" name="create_department">Add Department</button>
        </form>

        <form method="POST">
            <h3>Create New Semester</h3>
            <label for="semester_name">Semester Name:</label>
            <input type="text" id="semester_name" name="semester_name" required />

            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required />

            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required />

            <button type="submit" name="create_semester">Create Semester</button>
        </form>

        <h3>Existing Departments</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Department Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($departments): ?>
                    <?php foreach ($departments as $dept): ?>
                        <tr>
                            <td><?php echo (int)$dept['id']; ?></td>
                            <td><?php echo htmlspecialchars($dept['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2">No departments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h3>Existing Semesters</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Semester Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($semesters): ?>
                    <?php foreach ($semesters as $semester): ?>
                        <tr>
                            <td><?php echo (int)$semester['id']; ?></td>
                            <td><?php echo htmlspecialchars($semester['name']); ?></td>
                            <td><?php echo htmlspecialchars($semester['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($semester['end_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No semesters found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
