<?php
// Include the configuration file
require_once(__DIR__ . '/../config.php');

// Initialize variables
$username = '';
$error = '';

// Check if already logged in
if (is_logged_in()) {
    redirect('index.php');
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic input validation
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            // Verify credentials
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Successful login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Update last login time
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                // Redirect to dashboard
                redirect('index.php');
            } else {
                // Default admin login (for initial setup)
                if ($username === 'admin' && $password === 'IJ*gNVzjp9zI(oSh') {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = 'admin';
                    $_SESSION['admin_role'] = 'admin';
                    session_regenerate_id(true);
                    redirect('index.php');
                } else {
                    // Failed login
                    $error = "Invalid username or password";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error. Please try again later.";
            error_log('Login error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Admin Login</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= h($username) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
        
        <div class="mt-3 text-center">
            <small class="text-muted">Default login: admin / your-password</small>
        </div>
    </div>
</body>
</html>