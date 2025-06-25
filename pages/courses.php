<?php
require_once '../auth/auth.php';
require_once '../config/db.php';
require_role('tutor');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tutor_id = $_SESSION['user_id'];
$message = '';
$message_type = '';


$stmt = $pdo->prepare("
    SELECT c.id, c.name, c.code, s.name AS semester
    FROM course_assignments ca
    JOIN courses c ON ca.course_id = c.id
    JOIN semesters s ON ca.semester_id = s.id
    WHERE ca.tutor_id = ?
");
$stmt->execute([$tutor_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["login"])) {
    $course_id = $_POST["course_id"];
    $student_id = $_POST["student_id"];
    enroll_student($pdo, $student_id, $course_id, $tutor_id);
}

$students_stmt = $pdo->query("SELECT id, first_name, last_name, email FROM users WHERE role = 'student' ORDER BY first_name");
$students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

function get_students($pdo, $course_id) {
    $stmt = $pdo->prepare("
        SELECT u.id, u.first_name, u.last_name, u.email
        FROM enrollments e
        JOIN users u ON e.student_id = u.id
        WHERE e.course_id = ?
        ORDER BY u.first_name
    ");
    $stmt->execute([$course_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function enroll_student($pdo, $student_id, $course_id, $tutor_id) {
    global $message, $message_type;

    try {
        $auth = $pdo->prepare("SELECT 1 FROM course_assignments WHERE tutor_id = ? AND course_id = ?");
        $auth->execute([$tutor_id, $course_id]);
        if (!$auth->fetch()) {
            $message = "Unauthorized action.";
            $message_type = 'error';
            return false;
        }

        $exists = $pdo->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND course_id = ?");
        $exists->execute([$student_id, $course_id]);
        if ($exists->fetch()) {
            $message = "Student already enrolled.";
            $message_type = 'error';
            return false;
        }

        $insert = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $insert->execute([$student_id, $course_id]);

        $message = "Student enrolled successfully!";
        $message_type = 'success';
        return true;
    } catch (PDOException $e) {
        $message = "Enrollment failed: " . $e->getMessage();
        $message_type = 'error';
        return false;
    }
}

function unenroll_student($pdo, $student_id, $course_id, $tutor_id, &$msg, &$type) {
    $auth = $pdo->prepare("SELECT 1 FROM course_assignments WHERE tutor_id = ? AND course_id = ?");
    $auth->execute([$tutor_id, $course_id]);
    if (!$auth->fetch()) {
        $msg = "You do not have permission for this course.";
        $type = 'error';
        return false;
    }

    $delete = $pdo->prepare("DELETE FROM enrollments WHERE student_id = ? AND course_id = ?");
    $success = $delete->execute([$student_id, $course_id]);
    $msg = $success ? "Student unenrolled successfully." : "Unenrollment failed.";
    $type = $success ? 'success' : 'error';
    return $success;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? 0;
    $course_id = $_POST['course_id'] ?? 0;

    if (isset($_POST['enroll_student']) && $student_id && $course_id) {
        enroll_student($pdo, $student_id, $course_id, $tutor_id);
    }

    if (isset($_POST['unenroll_student']) && $student_id && $course_id) {
        unenroll_student($pdo, $student_id, $course_id, $tutor_id, $message, $message_type);
    }
}

// Build enrollments list
$enrollments = [];
foreach ($courses as $c) {
    $enrollments[$c['id']] = get_students($pdo, $c['id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Enrollment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body>
<div class="container">
  <h1>Manage Course Enrollments</h1>

  <?php if ($message): ?>
    <div class="message <?= htmlspecialchars($message_type) ?>"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" action="courses.php" novalidate>
    <h2>Enroll a Student</h2>

    <label>Select Student</label>
    <select name="student_id" required>
      <option value="" disabled selected>Select Student</option>
      <?php foreach ($students as $s): ?>
        <option value="<?= htmlspecialchars($s['id']) ?>">
          <?= htmlspecialchars("{$s['first_name']} {$s['last_name']} ({$s['email']})") ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Select Course</label>
    <select name="course_id" required>
      <option value="" disabled selected>Select Course</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?= htmlspecialchars($c['id']) ?>">
          <?= htmlspecialchars("{$c['code']} - {$c['name']} ({$c['semester']})") ?>
        </option>
      <?php endforeach; ?>
    </select>

    <input type="submit" name="login" value="Enroll Student" />
  </form>

  <h2>Enrolled Students</h2>
  <?php foreach ($courses as $c): ?>
    <div>
      <h3 class="course-title">
        <?= htmlspecialchars("{$c['code']} - {$c['name']} ({$c['semester']})") ?>
      </h3>
      <?php if (empty($enrollments[$c['id']])): ?>
        <p style="font-style: italic; color: #888;">No students enrolled yet.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>Name</th><th>Email</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php foreach ($enrollments[$c['id']] as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
              <td><?= htmlspecialchars($s['email']) ?></td>
              <td>
                <form method="post" style="margin:0;">
                  <input type="hidden" name="student_id" value="<?= htmlspecialchars($s['id']) ?>">
                  <input type="hidden" name="course_id" value="<?= htmlspecialchars($c['id']) ?>">
                  <input type="submit" name="unenroll_student" class="unenroll-btn" value="Unenroll" />
                  <input type="hidden" name="enroll_student" value="1">
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
