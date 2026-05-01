<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
include 'includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = md5($_POST['password'] ?? '');
    
    $stmt = $conn->prepare("SELECT id, full_name, role, email FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Incident Reporting System | Ureen Jamil & Aiman</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Cyber Incident Reporting System</h1>
                <p>AI-Assisted Incident Reporting & Threat Alert System</p>
                <div class="developer-badge">
                    Developed by: Ureen Jamil & Aiman | WEB-351 CCP
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>
            
            <div class="demo-credentials">
                <p><strong>Demo Credentials:</strong></p>
                <ul>
                    <li><strong>Ureen Jamil (Employee):</strong> ureen@example.com / 1234</li>
                    <li><strong>Aiman (Employee):</strong> aiman@example.com / 1234</li>
                    <li><strong>Security Team:</strong> security@example.com / 1234</li>
                    <li><strong>Admin:</strong> admin@example.com / 1234</li>
                </ul>
            </div>
            
            <div class="footer">
                <p>&copy; 2025 Cyber Incident System | Developed by Ureen Jamil & Aiman | Semester Project</p>
            </div>
        </div>
    </div>
</body>
</html>