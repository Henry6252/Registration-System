<?php
require_once '../auth/auth.php';
require_role('student');
require_once '../config/db.php';

$student_id = $_SESSION['user_id'] ?? null;

// Fetch student courses
$courses = $pdo->prepare("
    SELECT c.id, c.name AS course_name, c.code, u.username AS tutor_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    JOIN course_assignments ca ON ca.course_id = c.id
    JOIN users u ON ca.tutor_id = u.id
    WHERE e.student_id = :student_id
");
$courses->execute(['student_id' => $student_id]);
$student_courses = $courses->fetchAll(PDO::FETCH_ASSOC);

// Handle assignment upload
$upload_success = $upload_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['assignment'])) {
    $course_id = $_POST['course_id'] ?? '';
    $file = $_FILES['assignment'];

    if ($file['error'] === 0 && $course_id) {
        $upload_dir = '../assets/uploads/assignments/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = basename($file['name']);
        $file_path = $upload_dir . uniqid() . "_" . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $pdo->prepare("INSERT INTO assignments (student_id, course_id, file_name, file_path) VALUES (:student_id, :course_id, :file_name, :file_path)");
            $stmt->execute([
                'student_id' => $student_id,
                'course_id' => $course_id,
                'file_name' => $file_name,
                'file_path' => $file_path
            ]);
            $upload_success = "✅ Assignment uploaded successfully.";
        } else {
            $upload_error = "❌ Failed to upload the file.";
        }
    } else {
        $upload_error = "❌ Invalid upload or missing course.";
    }
}
?>

<link rel="stylesheet" href="../assets/css/global.css">

<div class="student-section" style="max-width: 1000px; margin: auto; padding: 20px;">

    <?php if ($upload_success): ?>
        <div class="success" style="color: green; margin-bottom: 15px;"><?php echo htmlspecialchars($upload_success); ?></div>
    <?php endif; ?>

    <?php if ($upload_error): ?>
        <div class="error" style="color: red; margin-bottom: 15px;"><?php echo htmlspecialchars($upload_error); ?></div>
    <?php endif; ?>

    <?php if ($student_courses): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <?php foreach ($student_courses as $course): ?>
                <div class="course-card" style="border: 1px solid #ddd; border-radius: 10px; padding: 20px; background: #f9f9f9;">
                    <h3>
                        <a href="course_details.php?course_id=<?php echo $course['id']; ?>" style="color: #2a4cb2; text-decoration: none;">
                            <?php echo htmlspecialchars($course['course_name']); ?> (<?php echo htmlspecialchars($course['code']); ?>)
                        </a>
                    </h3>
                    <p><strong>Tutor:</strong> <?php echo htmlspecialchars($course['tutor_name']); ?></p>

                    <div class="course-materials">
                        <strong>Materials:</strong>
                        <ul style="padding-left: 18px; margin: 10px 0;">
                            <?php
                            $materials = $pdo->prepare("SELECT file_name, file_path FROM course_materials WHERE course_id = :course_id");
                            $materials->execute(['course_id' => $course['id']]);
                            $files = $materials->fetchAll(PDO::FETCH_ASSOC);
                            if ($files):
                                foreach ($files as $file): ?>
                                    <li>
                                        <a class="download-link" href="<?php echo htmlspecialchars($file['file_path']); ?>" download>
                                            <?php echo htmlspecialchars($file['file_name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach;
                            else:
                                echo '<li style="color: #777;">No materials uploaded yet.</li>';
                            endif;
                            ?>
                        </ul>
                    </div>

                    <div class="assignment-form" style="margin-top: 10px;">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <label>Submit Assignment:</label><br>
                            <input type="file" name="assignment" required style="margin: 5px 0;"><br>
                            <button type="submit" style="padding: 6px 12px; background: #4CAF50; color: #fff; border: none; border-radius: 5px;">
                                Upload
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color: #777;">You are not enrolled in any courses.</p>
    <?php endif; ?>

</div>
