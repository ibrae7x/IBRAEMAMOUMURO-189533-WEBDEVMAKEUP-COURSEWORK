<?php
/**
 * Index Page - Sign-in Form
 * 
 * This is the main entry point of the application
 * Users can sign in using their username and password
 */

require_once 'config/constants.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';
require_once 'models/User.php';

// If user is already logged in, redirect to dashboard
if (SessionManager::isLoggedIn()) {
    header('Location: dashboard/dashboard.php');
    exit();
}

$error = '';
$success = '';

// Handle GET parameters
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'session_expired':
            $error = 'Your session has expired. Please log in again.';
            break;
        case 'access_denied':
            $error = 'Access denied. You do not have permission to access that resource.';
            break;
        default:
            $error = 'An error occurred. Please try again.';
    }
}

if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'logged_out':
            $success = 'You have been successfully logged out.';
            break;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $user = new User();
        
        if ($user->authenticate($username, $password)) {
            SessionManager::login($user);
            logActivity("User logged in: " . $username);
            
            // Redirect to dashboard
            header('Location: dashboard/dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password.';
            logActivity("Failed login attempt: " . $username, 'WARNING');
        }
    }
}

$pageTitle = 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-users-cog fa-3x text-primary mb-3"></i>
                <h2><?php echo APP_NAME; ?></h2>
                <p>Please sign in to access the system</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-1"></i>Username
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        value="<?php echo htmlspecialchars($username ?? ''); ?>"
                        required
                        autocomplete="username"
                        placeholder="Enter your username"
                    >
                    <div class="invalid-feedback">
                        Please enter your username.
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i>Password
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        >
                        <button 
                            class="btn btn-outline-secondary" 
                            type="button" 
                            id="togglePassword"
                            onclick="togglePasswordVisibility()"
                        >
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">
                        Please enter your password.
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>
</html>
