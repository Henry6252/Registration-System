<?php
require_once '../auth/auth.php';
require_role('tutor');
require_once '../config/db.php';

$tutor_id = $_SESSION['user_id'];
$message = "";

// Fetch assigned courses for tutor
$stmt = $pdo->prepare("
    SELECT c.id, c.name, c.code
    FROM course_assignments ca
    JOIN courses c ON ca.course_id = c.id
    WHERE ca.tutor_id = ?
");
$stmt->execute([$tutor_id]);
$assigned_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle new assignment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_assignment'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $course_id = $_POST['course_id'];

    $stmt = $pdo->prepare("INSERT INTO assignments (course_id, tutor_id, title, description, due_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$course_id, $tutor_id, $title, $description, $due_date]);

    $message = "Assignment created successfully.";
}

// Fetch tutor's assignments
$stmt = $pdo->prepare("SELECT a.*, c.name AS course_name FROM assignments a JOIN courses c ON a.course_id = c.id WHERE tutor_id = ?");
$stmt->execute([$tutor_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle grading submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_submission'])) {
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];

    $stmt = $pdo->prepare("UPDATE submissions SET grade = ?, feedback = ? WHERE id = ?");
    $stmt->execute([$grade, $feedback, $submission_id]);
    $message = "Grade and feedback submitted.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assignments</title>
    <link rel="stylesheet" href="../assets/css/ass.css" />
</head>
<body>
<div class="container">
    <h2>Assignment Management</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="section">
        <h3>Create New Assignment</h3>
        <form method="POST">
            <label>Course</label>
            <select name="course_id" required>
                <?php foreach ($assigned_courses as $course): ?>
                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?> (<?= htmlspecialchars($course['code']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <label>Title</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description" required></textarea>

            <label>Due Date</label>
            <input type="datetime-local" name="due_date" required>

            <button type="submit" name="create_assignment">Create Assignment</button>
        </form>
    </div>

    <div class="section">
        <h3>My Assignments</h3>
        <?php foreach ($assignments as $assignment): ?>
            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                <strong><?= htmlspecialchars($assignment['title']) ?></strong> - <?= htmlspecialchars($assignment['course_name']) ?><br>
                <em>Due: <?= date("M d, Y H:i", strtotime($assignment['due_date'])) ?></em><br>
                <p><?= nl2br(htmlspecialchars($assignment['description'])) ?></p>

                <h4>Submissions:</h4>
                <?php
                    $stmt = $pdo->prepare("
                        SELECT s.*, u.first_name, u.last_name
                        FROM submissions s
                        JOIN users u ON s.student_id = u.id
                        WHERE s.assignment_id = ?
                    ");
                    $stmt->execute([$assignment['id']]);
                    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if ($submissions): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Submitted At</th>
                                <th>File</th>
                                <th>Grade</th>
                                <th>Feedback</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?= htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name']) ?></td>
                                <td><?= date("Y-m-d H:i", strtotime($submission['submitted_at'])) ?></td>
                                <td><a href="<?= htmlspecialchars($submission['submitted_file']) ?>" target="_blank">Download</a></td>
                                <td><?= htmlspecialchars($submission['grade']) ?></td>
                                <td><?= nl2br(htmlspecialchars($submission['feedback'])) ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                                        <input type="text" name="grade" placeholder="e.g. 85%" value="<?= htmlspecialchars($submission['grade']) ?>" required>
                                        <textarea name="feedback" placeholder="Feedback..."><?= htmlspecialchars($submission['feedback']) ?></textarea>
                                        <button type="submit" name="grade_submission">Submit</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No submissions yet.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
