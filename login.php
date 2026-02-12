<?php
include "config.php";

// Handle form submission
$message = '';
if ($_POST) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($q);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit();
    } else {
        $message = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PG Rental</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-building"></i>
                </div>
                PG Rental
            </a>
            <div class="nav-links">
                <a href="register.php" class="btn btn-outline">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="form-container fade-in">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                    <i class="fas fa-sign-in-alt fa-2x" style="color: white;"></i>
                </div>
                <h2 class="form-title">Welcome Back</h2>
                <p class="text-muted">Sign in to your account to continue</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" style="margin-right: 0.5rem;">
                        Keep me signed in
                    </label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </div>
            </form>

            <div class="text-center" style="margin-top: 2rem;">
                <p class="text-muted">Don't have an account? <a href="register.php" class="text-primary">Register here</a></p>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                <div style="display: flex; gap: 1rem; justify-content: center; text-align: center;">
                    <div style="flex: 1; padding: 1rem; background: rgba(59, 130, 246, 0.1); border-radius: var(--radius-md);">
                        <i class="fas fa-shield-alt fa-2x text-primary" style="margin-bottom: 1rem; display: block;"></i>
                        <h4>Secure Login</h4>
                        <p class="text-muted" style="font-size: 0.875rem;">Your data is protected with industry-standard security</p>
                    </div>
                    <div style="flex: 1; padding: 1rem; background: rgba(16, 185, 129, 0.1); border-radius: var(--radius-md);">
                        <i class="fas fa-mobile-alt fa-2x text-accent" style="margin-bottom: 1rem; display: block;"></i>
                        <h4>Easy Access</h4>
                        <p class="text-muted" style="font-size: 0.875rem;">Access your bookings from any device</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer style="margin-top: auto; padding: 2rem 0; border-top: 1px solid var(--border-color); color: var(--text-secondary);">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> PG Rental. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add form validation and animations
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = document.querySelectorAll('.form-input');
            
            // Add focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.parentElement.style.transform = 'translateY(-2px)';
                    input.parentElement.style.transition = 'transform 0.3s ease';
                });
                
                input.addEventListener('blur', () => {
                    input.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Add password visibility toggle
            const passwordField = document.getElementById('password');
            if (passwordField) {
                const toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
                toggleBtn.style.position = 'absolute';
                toggleBtn.style.right = '10px';
                toggleBtn.style.top = '50%';
                toggleBtn.style.transform = 'translateY(-50%)';
                toggleBtn.style.background = 'none';
                toggleBtn.style.border = 'none';
                toggleBtn.style.cursor = 'pointer';
                toggleBtn.style.color = 'var(--text-secondary)';
                
                const inputWrapper = passwordField.parentElement;
                inputWrapper.style.position = 'relative';
                inputWrapper.appendChild(toggleBtn);
                
                toggleBtn.addEventListener('click', () => {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    toggleBtn.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }
        });
    </script>
</body>
</html>
