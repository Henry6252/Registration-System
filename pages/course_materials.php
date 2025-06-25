<?php
require_once '../auth/auth.php';
require_once '../config/db.php';
require_role('student');

$student_id = $_SESSION['user_id'] ?? null;

// Fetch student-enrolled courses and their materials
$query = $pdo->prepare("
    SELECT c.id AS course_id, c.name AS course_name, c.code AS course_code, cm.file_name, cm.file_path
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    LEFT JOIN course_materials cm ON c.id = cm.course_id
    WHERE e.student_id = :student_id
    ORDER BY c.name
");
$query->execute(['student_id' => $student_id]);
$materials = $query->fetchAll(PDO::FETCH_ASSOC);

// Group materials by course
$grouped = [];
foreach ($materials as $row) {
    $cid = $row['course_id'];
    if (!isset($grouped[$cid])) {
        $grouped[$cid] = [
            'name' => $row['course_name'],
            'code' => $row['course_code'],
            'files' => []
        ];
    }
    if ($row['file_name'] && $row['file_path']) {
        $grouped[$cid]['files'][] = [
            'name' => $row['file_name'],
            'path' => $row['file_path']
        ];
    }
}
?>

<link rel="stylesheet" href="../assets/css/global.css">
<div class="course-materials-section">
    <h2>Course Materials</h2>

    <?php if (!empty($grouped)): ?>
        <?php foreach ($grouped as $course): ?>
            <div class="course-box">
                <h3><?php echo htmlspecialchars($course['name']) . ' (' . htmlspecialchars($course['code']) . ')'; ?></h3>

                <?php if (!empty($course['files'])): ?>
                    <ul>
                        <?php foreach ($course['files'] as $file): ?>
                            <li>
                                <a class="download-link" href="<?php echo htmlspecialchars($file['path']); ?>" download>
                                    <?php echo htmlspecialchars($file['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="no-materials">No materials uploaded yet for this course.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-materials">You are not enrolled in any courses with available materials.</p>
    <?php endif; ?>
</div>
