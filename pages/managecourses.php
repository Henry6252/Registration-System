<?php
require_once '../auth/auth.php';
require_role('admin');
require_once '../config/db.php';

// Fetch data
$tutors = $pdo->query("SELECT id, username FROM users WHERE role = 'tutor' ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$semesters = $pdo->query("SELECT id, name, start_date, end_date FROM semesters ORDER BY start_date DESC")->fetchAll(PDO::FETCH_ASSOC);
$courses_list = $pdo->query("SELECT id, name FROM courses ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success = '';

// Handle course creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_course'])) {
    $course_name = trim($_POST['course_name'] ?? '');
    $course_code = trim($_POST['course_code'] ?? '');
    $department_id = $_POST['department_id'] ?? '';

    if (!$course_name) $errors[] = "Course name is required.";
    if (!$course_code) $errors[] = "Course code is required.";
    if (!$department_id) $errors[] = "Department is required.";

    if (!$errors) {
        $check = $pdo->prepare("SELECT id FROM courses WHERE name = :name AND department_id = :department_id");
        $check->execute(['name' => $course_name, 'department_id' => $department_id]);
        if ($check->fetchColumn()) {
            $errors[] = "Course already exists in this department.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO courses (name, code, department_id) VALUES (:name, :code, :department_id)");
            $stmt->execute([
                'name' => $course_name,
                'code' => $course_code,
                'department_id' => $department_id
            ]);
            $success = "Course created successfully!";
        }
    }
}

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_course'])) {
    $course_id = $_POST['course_id'] ?? '';
    $tutor_id = $_POST['tutor_id'] ?? '';
    $semester_id = $_POST['semester_id'] ?? '';

    if (!$course_id) $errors[] = "Please select a course.";
    if (!$tutor_id) $errors[] = "Please select a tutor.";
    if (!$semester_id) $errors[] = "Please select a semester.";

    if (!$errors) {
        $check = $pdo->prepare("SELECT 1 FROM course_assignments WHERE course_id = :course_id AND tutor_id = :tutor_id AND semester_id = :semester_id");
        $check->execute([
            'course_id' => $course_id,
            'tutor_id' => $tutor_id,
            'semester_id' => $semester_id
        ]);
        if ($check->fetch()) {
            $errors[] = "This course is already assigned to this tutor in the selected semester.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO course_assignments (course_id, tutor_id, semester_id) VALUES (:course_id, :tutor_id, :semester_id)");
            $stmt->execute([
                'course_id' => $course_id,
                'tutor_id' => $tutor_id,
                'semester_id' => $semester_id
            ]);
            $success = "Course assigned to tutor successfully!";
        }
    }
}

$courses = $pdo->query("
    SELECT c.name AS course_name, d.name AS department_name, u.username AS tutor_name, s.name AS semester_name, s.start_date, s.end_date
    FROM course_assignments ca
    JOIN courses c ON ca.course_id = c.id
    JOIN departments d ON c.department_id = d.id
    JOIN users u ON ca.tutor_id = u.id
    JOIN semesters s ON ca.semester_id = s.id
    ORDER BY c.name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="../assets/css/dasboard.css" />
    <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="../dashboards/admin_dashboard.php">Dashboard Home</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="managecourses.php" class="active">Manage Courses</a></li>
        <li><a href="departments.php">Departments</a></li>
    </ul>
</div>

<div class="main">
    <div class="navbar">
        <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

    <div class="dashboard-content">
        <h2>Manage Courses</h2>

        <div class="messages">
            <?php foreach ($errors as $error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
            <?php if ($success): ?>
                <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
        </div>

        <!-- Course Creation Form -->
        <form method="POST">
            <h3>Create New Course</h3>
            <input type="hidden" name="create_course" value="1" />
            <label for="course_name">Course Name:</label>
            <input type="text" name="course_name" id="course_name" required />
            <label for="course_code">Course Code:</label>
            <input type="text" name="course_code" id="course_code" required />
            <label for="department_id">Department:</label>
            <select name="department_id" required>
                <option value="">Select Department</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Create Course</button>
        </form>

        <!-- Course Assignment Form -->
        <form method="POST">
            <h3>Assign Course to Tutor</h3>
            <input type="hidden" name="assign_course" value="1" />
            <label for="course_id">Select Course:</label>
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php foreach ($courses_list as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="tutor_id">Select Tutor:</label>
            <select name="tutor_id" required>
                <option value="">Select Tutor</option>
                <?php foreach ($tutors as $tutor): ?>
                    <option value="<?php echo $tutor['id']; ?>"><?php echo htmlspecialchars($tutor['username']); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="semester_id">Select Semester:</label>
            <select name="semester_id" required>
                <option value="">Select Semester</option>
                <?php foreach ($semesters as $sem): ?>
                    <option value="<?php echo $sem['id']; ?>">
                        <?php echo htmlspecialchars($sem['name']) . " (" . $sem['start_date'] . " to " . $sem['end_date'] . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Assign Course</button>
        </form>

        <h3>Existing Course Assignments</h3>
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Department</th>
                    <th>Assigned Tutor</th>
                    <th>Semester</th>
                    <th>Period</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($courses): ?>
                    <?php foreach ($courses as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['tutor_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['semester_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['start_date'] . " to " . $c['end_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No assignments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
