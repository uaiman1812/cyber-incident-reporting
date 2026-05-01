<?php
include 'includes/auth.php';
requireRole('admin');
include 'includes/db.php';

$message = '';
$error = '';

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $department = $_POST['department'];
    
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $department);
    if ($stmt->execute()) {
        $message = "User added successfully";
    } else {
        $error = "Failed to add user";
    }
}

// Handle delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = "User deleted";
    }
}

// Get all users
$users = $conn->query("SELECT id, full_name, email, role, department, created_at FROM users ORDER BY created_at DESC");

// Get logs
$logs = $conn->query("SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 50");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Cyber Incident System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Ureen Jamil & Aiman</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="admin_panel.php" class="nav-item active">Admin Panel</a>
                <a href="logout.php" class="nav-item">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="user-info">
                    <span>Admin: <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card">
                <h2>Add New User</h2>
                <form method="POST" class="form-inline">
                    <input type="text" name="full_name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <select name="role">
                        <option value="employee">Employee</option>
                        <option value="security">Security</option>
                        <option value="admin">Admin</option>
                    </select>
                    <input type="text" name="department" placeholder="Department">
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </form>
            </div>

            <div class="card">
                <h2>Manage Users</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['role']; ?></td>
                                <td><?php echo htmlspecialchars($user['department']); ?></td>
                                <td><?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('Delete user?')" class="btn-danger-small">Delete</a>
                                <?php endif; ?><tr>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h2>System Logs</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr><th>Time</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($log = $logs->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $log['created_at']; ?></td>
                                <td><?php echo htmlspecialchars($log['user_email']); ?></td>
                                <td><?php echo $log['action']; ?></td>
                                <td><?php echo htmlspecialchars(substr($log['details'], 0, 50)); ?></td>
                                <td><?php echo $log['ip_address']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="footer-credit">
                <p>Cyber Incident Reporting System | Developed by Ureen Jamil & Aiman | WEB ENGINEERING CCP | Fall 2025</p>
            </div>
        </main>
    </div>
</body>
</html>