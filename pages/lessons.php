<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../auth/auth.php';
require_once '../config/db.php';
require_role('tutor');


$tutor_id = $_SESSION['user_id'] ?? null;
$upload_message = '';
$lesson_message = '';

// Load tutor's assigned courses
try {
    $stmt = $pdo->prepare("SELECT c.id, c.name FROM courses c 
        JOIN course_assignments ca ON ca.course_id = c.id 
        WHERE ca.tutor_id = :tutor_id");
    $stmt->execute(['tutor_id' => $tutor_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<pre>❌ Course Fetch Exception:\n"; print_r($e); echo "</pre>";
    die("<p style='color:red;'>❌ Failed to fetch courses: " . htmlspecialchars($e->getMessage()) . "</p>");
}

// Handle material upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_material'])) {
    echo "<pre>🔍 Upload POST:\n"; print_r($_POST); echo "</pre>";
    $course_id = $_POST['course_id'] ?? '';
    $file = $_FILES['material_file'] ?? null;

    if ($course_id && $file && $file['error'] === 0) {
        $upload_dir = '../assets/uploads/materials/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_name = basename($file['name']);
        $file_path = $upload_dir . uniqid('mat_') . '_' . $file_name;

        if (is_writable($upload_dir) && move_uploaded_file($file['tmp_name'], $file_path)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO course_materials (course_id, file_name, file_path)
                    VALUES (:course_id, :file_name, :file_path)");
                $stmt->execute([
                    'course_id' => $course_id,
                    'file_name' => $file_name,
                    'file_path' => $file_path
                ]);
                $upload_message = "<p style='color:green;'>✅ Material uploaded successfully.</p>";
            } catch (PDOException $e) {
                echo "<pre>❌ Upload Exception:\n"; print_r($e); echo "</pre>";
                $upload_message = "<p style='color:red;'>❌ DB Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            $upload_message = "<p style='color:red;'>❌ Upload failed. Directory not writable or move failed.</p>";
        }
    } else {
        $upload_message = "<p style='color:red;'>❌ Please select a course and valid file.</p>";
    }
}

// Handle lesson creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_lesson'])) {
    echo "<pre>🔍 Lesson POST:\n"; print_r($_POST); echo "</pre>";
    $course_id = $_POST['lesson_course_id'] ?? '';
    $title = trim($_POST['lesson_title'] ?? '');
    $content = trim($_POST['lesson_content'] ?? '');

    if ($course_id && $title && $content) {
        try {
            $stmt = $pdo->prepare("INSERT INTO lessons (course_id, tutor_id, title, content) 
                VALUES (:course_id, :tutor_id, :title, :content)");
            $stmt->execute([
                'course_id' => $course_id,
                'tutor_id' => $tutor_id,
                'title' => $title,
                'content' => $content
            ]);
            $lesson_message = "<p style='color:green;'>✅ Lesson saved successfully.</p>";
        } catch (PDOException $e) {
            echo "<pre>❌ Lesson Exception:\n"; print_r($e); echo "</pre>";
            $lesson_message = "<p style='color:red;'>❌ DB Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $lesson_message = "<p style='color:red;'>❌ Please fill all lesson fields.</p>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Upload & Lessons</title>
    <style>
        body {
            background: #f4f4f4;
            font-family: sans-serif;
        }
        .container {
            background: #fff;
            padding: 20px 30px;
            max-width: 700px;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        button {
            padding: 10px 20px;
            background: #2980b9;
            border: none;
            color: white;
            border-radius: 5px;
        }
        .success { color: green; }
        .error { color: red; }
        hr {
            margin: 40px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📁 Upload Course Materials</h2>
    <?= $upload_message ?>
    <form method="POST"  action="lessons.php" enctype="multipart/form-data">
        <label for="course_id">Course:</label>
        <select name="course_id" id="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="material_file">File:</label>
        <input type="file" name="material_file" id="material_file" required>

        <button type="submit" name="upload_material">Upload</button>
    </form>

    <hr>

    <h2>📝 Create a Lesson</h2>
    <?= $lesson_message ?>
    <form method="POST" action="/newproject/pages/lessons.php" >
        <label for="lesson_course_id">Course:</label>
        <select name="lesson_course_id" id="lesson_course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="lesson_title">Title:</label>
        <input type="text" name="lesson_title" id="lesson_title" required>

        <label for="lesson_content">Content:</label>
        <textarea name="lesson_content" id="lesson_content" rows="6" required></textarea>

        <button type="submit" name="create_lesson" style="background: #27ae60;">Save Lesson</button>
    </form>
</div>
</body>
</html>
