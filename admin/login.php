<?php
require_once __DIR__ . '/../config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Handle login form submission
if (isPostRequest()) {
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Read admins from JSON
        $admins = readJSON(ADMINS_FILE);
        
        $loginSuccess = false;
        $adminData = null;
        
        // Find admin by email
        foreach ($admins as $admin) {
            if ($admin['email'] === $email) {
                // Verify password
                if (password_verify($password, $admin['password'])) {
                    $loginSuccess = true;
                    $adminData = $admin;
                    break;
                }
            }
        }
        
        if ($loginSuccess) {
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $adminData['id'];
            $_SESSION['admin_name'] = $adminData['name'];
            $_SESSION['admin_email'] = $adminData['email'];
            $_SESSION['last_activity'] = time();
            
            // Redirect to dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

// Check for timeout message
$timeoutMsg = isset($_GET['timeout']) ? 'Your session has expired. Please login again.' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Central Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>Central Admin Dashboard</h1>
                <p>Please login to continue</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($timeoutMsg): ?>
                <div class="alert alert-warning">
                    <?php echo htmlspecialchars($timeoutMsg); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" class="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="admin@example.com"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Enter your password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="login-footer">
                <p class="text-muted">Default credentials: admin@example.com / password</p>
            </div>
        </div>
    </div>
</body>
</html>
