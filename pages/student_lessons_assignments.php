<?php
require_once '../auth/auth.php';
require_role('student');
require_once '../config/db.php';

$student_id = $_SESSION['user_id'] ?? null;

// Fetch enrolled courses
$courses = $pdo->prepare("
    SELECT c.id, c.name AS course_name 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    WHERE e.student_id = :student_id
");
$courses->execute(['student_id' => $student_id]);
$courses = $courses->fetchAll(PDO::FETCH_ASSOC);

// Fetch lessons and assignments
$course_ids = array_column($courses, 'id');
$in_clause = implode(',', array_fill(0, count($course_ids), '?'));

$lessons = [];
$assignments = [];

if (!empty($course_ids)) {
    $lessonStmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id IN ($in_clause)");
    $lessonStmt->execute($course_ids);
    $lessons = $lessonStmt->fetchAll(PDO::FETCH_ASSOC);

    $assignmentStmt = $pdo->prepare("SELECT * FROM assignments WHERE course_id IN ($in_clause)");
    $assignmentStmt->execute($course_ids);
    $assignments = $assignmentStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle file upload
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'])) {
    $assignment_id = $_POST['assignment_id'];
    $file = $_FILES['submission_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/';
        $filename = $upload_dir . time() . '_' . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $filename)) {
            $insert = $pdo->prepare("
                INSERT INTO submissions (assignment_id, student_id, submitted_file) 
                VALUES (:assignment_id, :student_id, :submitted_file)
            ");
            $insert->execute([
                'assignment_id' => $assignment_id,
                'student_id' => $student_id,
                'submitted_file' => $filename,
            ]);
            $message = "✅ Assignment submitted successfully!";
        } else {
            $message = "❌ Failed to move uploaded file.";
        }
    } else {
        $message = "❌ File upload error.";
    }
}
?>

<link rel="stylesheet" href="../assets/css/global.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>
    .container {
        padding: 2rem;
        max-width: 1200px;
        margin: auto;
    }
    h1, h2 {
        color: #2c3e50;
    }
    .message {
        margin: 1rem 0;
        padding: 0.75rem 1rem;
        background-color: #ecf0f1;
        border-left: 5px solid #2ecc71;
        color: #2d3436;
        border-radius: 5px;
    }
    .lesson, .assignment {
        background-color: #fff;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin: 1rem 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .lesson-title, .assignment-title {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .assignment-form {
        margin-top: 1rem;
    }
    .assignment-form input[type="file"] {
        margin: 0.5rem 0;
        padding: 0.3rem;
    }
    .submit-btn {
        padding: 0.6rem 1.2rem;
        background-color: #3498db;
        border: none;
        color: white;
        cursor: pointer;
        border-radius: 5px;
        font-weight: bold;
    }
    .submit-btn:hover {
        background-color: #2980b9;
    }
    small {
        display: block;
        margin-top: 0.5rem;
        color: #7f8c8d;
    }
    @media (max-width: 600px) {
        .container {
            padding: 1rem;
        }
    }
</style>

<div class="container">
    <h1>📚 Your Lessons & Assignments</h1>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h2>Lessons</h2>
    <?php if ($lessons): ?>
        <?php foreach ($lessons as $lesson): ?>
            <div class="lesson">
                <div class="lesson-title"><?= htmlspecialchars($lesson['title']) ?></div>
                <div><?= nl2br(htmlspecialchars($lesson['content'])) ?></div>
                <small>Updated: <?= date('M d, Y', strtotime($lesson['updated_at'])) ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No lessons available at the moment.</p>
    <?php endif; ?>

    <h2>Assignments</h2>
    <?php if ($assignments): ?>
        <?php foreach ($assignments as $assignment): ?>
            <div class="assignment">
                <div class="assignment-title"><?= htmlspecialchars($assignment['title']) ?></div>
                <div><?= nl2br(htmlspecialchars($assignment['description'])) ?></div>
                <small>Due: <?= date('M d, Y', strtotime($assignment['due_date'])) ?></small>

                <form class="assignment-form" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                    <label>Upload Your Work:</label>
                    <input type="file" name="submission_file" required>
                    <button type="submit" class="submit-btn">Submit & Mark as Done</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No assignments available at the moment.</p>
    <?php endif; ?>
</div>
