<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../auth/auth.php';
require_role('admin');
require_once '../config/db.php';

$search = $_GET['search'] ?? '';
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_user'])) {
        $id = (int)$_POST['user_id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = trim($_POST['role']);

        if ($username && $email && $role) {
            $updateSql = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
            $stmt = $pdo->prepare($updateSql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':role' => $role,
                ':id' => $id,
            ]);
            $message = "User #$id updated successfully.";
        } else {
            $message = "Please fill all fields for editing user.";
        }
    }

    if (isset($_POST['reset_password'])) {
        $id = (int)$_POST['user_id'];
        $defaultPassword = password_hash("default123", PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :pwd WHERE id = :id");
        $stmt->execute([':pwd' => $defaultPassword, ':id' => $id]);
        $message = "Password for user #$id reset to default.";
    }

    if (isset($_POST['delete_user'])) {
        $id = (int)$_POST['user_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = "User #$id deleted successfully.";
    }

    if (isset($_POST['upload_single_student'])) {
        $username = trim($_POST['student_username']);
        $email = trim($_POST['student_email']);
        $password = password_hash("default123", PASSWORD_DEFAULT);
        $role = 'student';

        if ($username && $email) {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $password,
                ':role' => $role
            ]);
            $message = "Student '$username' added successfully.";
        } else {
            $message = "Please provide both username and email for the student.";
        }
    }

    if (isset($_POST['upload_csv'])) {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($fileTmpPath, 'r');
            $rowCount = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 2) {
                    $username = trim($data[0]);
                    $email = trim($data[1]);

                    if ($username && $email) {
                        $password = password_hash("default123", PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'student')");
                        $stmt->execute([
                            ':username' => $username,
                            ':email' => $email,
                            ':password' => $password
                        ]);
                        $rowCount++;
                    }
                }
            }

            fclose($handle);
            $message = "$rowCount students added from CSV.";
        } else {
            $message = "CSV upload failed.";
        }
    }
}

$sql = "SELECT id, username, email, role FROM users WHERE username ILIKE :search OR email ILIKE :search ORDER BY id";
$stmt = $pdo->prepare($sql);
$like = "%$search%";
$stmt->bindParam(':search', $like);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$action = $_GET['action'] ?? '';
$editUser = null;
if (($action === 'edit' || $action === 'reset') && isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    foreach ($users as $u) {
        if ($u['id'] === $userId) {
            $editUser = $u;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/dasboard.css" />
    <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="../dashboards/admin_dashboard.php">Dashboard Home</a></li>
        <li><a href="manage users.php">Manage Users</a></li>
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
        <h2>Manage Users</h2>

        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form class="search-form" method="get">
            <input type="text" name="search" placeholder="Search by username or email" value="<?php echo htmlspecialchars($search); ?>" />
            <button type="submit">Search</button>
        </form>

        <div class="upload-forms">
            <form class="upload-form" method="POST">
                <h3>Upload Single Student</h3>
                <label for="student_username">Username</label>
                <input type="text" name="student_username" required />
                <label for="student_email">Email</label>
                <input type="email" name="student_email" required />
                <button type="submit" name="upload_single_student">Add Student</button>
            </form>

            <form class="upload-form" method="POST" enctype="multipart/form-data">
                <h3>Upload CSV File</h3>
                <label for="csv_file">CSV file (username,email)</label>
                <input type="file" name="csv_file" accept=".csv" required />
                <button type="submit" name="upload_csv">Upload Students</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo (int)$user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td class="actions">
                                <button onclick="window.location.href='edit_user.php?action=edit&id=<?php echo (int)$user['id']; ?>&search=<?php echo urlencode($search); ?>'">Edit</button>
                                <button onclick="window.location.href='edit_user.php?action=reset&id=<?php echo (int)$user['id']; ?>&search=<?php echo urlencode($search); ?>'">Reset Password</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                                    <button type="submit" name="delete_user" style="background-color:#c0392b;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($action === 'edit' && $editUser): ?>
    <div class="modal-bg" onclick="closeModal(event)">
        <div class="modal" onclick="event.stopPropagation()">
            <span class="close-btn" onclick="closeModal()">×</span>
            <h3>Edit User #<?php echo (int)$editUser['id']; ?></h3>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo (int)$editUser['id']; ?>" />
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($editUser['username']); ?>" />
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($editUser['email']); ?>" />
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin" <?php if ($editUser['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="tutor" <?php if ($editUser['role'] === 'tutor') echo 'selected'; ?>>Tutor</option>
                    <option value="student" <?php if ($editUser['role'] === 'student') echo 'selected'; ?>>Student</option>
                </select>
                <button type="submit" name="edit_user">Save Changes</button>
            </form>
        </div>
    </div>
<?php elseif ($action === 'reset' && $editUser): ?>
    <div class="modal-bg" onclick="closeModal(event)">
        <div class="modal" onclick="event.stopPropagation()">
            <span class="close-btn" onclick="closeModal()">×</span>
            <h3>Reset Password for User #<?php echo (int)$editUser['id']; ?></h3>
            <p>Are you sure you want to reset the password for <strong><?php echo htmlspecialchars($editUser['username']); ?></strong> to the default password?</p>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo (int)$editUser['id']; ?>" />
                <button type="submit" name="reset_password">Confirm Reset</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
function closeModal(event) {
    if (event) event.preventDefault();
    const url = new URL(window.location);
    url.searchParams.delete('action');
    url.searchParams.delete('id');
    window.history.replaceState({}, document.title, url.toString());
    const modalBg = document.querySelector('.modal-bg');
    if (modalBg) modalBg.remove();
}
</script>

</body>
</html>
