<?php
include "config.php";

// Handle form submission
$message = '';
$errors = [];

if ($_POST) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } else {
        // Check if email already exists
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $errors[] = "Email address is already registered. Please use a different email or <a href='login.php' class='text-primary'>login here</a>.";
        }
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "Please enter a valid phone number (10-15 digits).";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users(name, email, phone, password) VALUES('$name', '$email', '$phone', '$hashed_password')";
        
        if (mysqli_query($conn, $query)) {
            $message = "success";
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PG Rental</title>
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
                <a href="login.php" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="form-container fade-in">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--accent-color), var(--primary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                    <i class="fas fa-user-plus fa-2x" style="color: white;"></i>
                </div>
                <h2 class="form-title">Create Your Account</h2>
                <p class="text-muted">Join us today and find your perfect room</p>
            </div>

            <?php if ($message === 'success'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Registration successful! You can now <a href="login.php" class="text-primary">login here</a>.
                </div>
            <?php elseif (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="register.php">
                <div class="form-group">
                    <label class="form-label" for="name">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" class="form-input" placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Create a strong password" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" id="terms" name="terms" required style="margin-right: 0.5rem;">
                        I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a>
                    </label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-accent w-full">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </div>
            </form>

            <div class="text-center" style="margin-top: 2rem;">
                <p class="text-muted">Already have an account? <a href="login.php" class="text-primary">Login here</a></p>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center;">
                    <div style="padding: 1rem; background: rgba(16, 185, 129, 0.1); border-radius: var(--radius-md);">
                        <i class="fas fa-shield-alt fa-2x text-accent" style="margin-bottom: 1rem; display: block;"></i>
                        <h4>Secure</h4>
                        <p class="text-muted" style="font-size: 0.875rem;">Your information is safe with us</p>
                    </div>
                    <div style="padding: 1rem; background: rgba(59, 130, 246, 0.1); border-radius: var(--radius-md);">
                        <i class="fas fa-clock fa-2x text-primary" style="margin-bottom: 1rem; display: block;"></i>
                        <h4>Fast</h4>
                        <p class="text-muted" style="font-size: 0.875rem;">Quick and easy registration process</p>
                    </div>
                    <div style="padding: 1rem; background: rgba(234, 179, 8, 0.1); border-radius: var(--radius-md);">
                        <i class="fas fa-mobile-alt fa-2x" style="color: #f59e0b; margin-bottom: 1rem; display: block;"></i>
                        <h4>Accessible</h4>
                        <p class="text-muted" style="font-size: 0.875rem;">Access from any device, anywhere</p>
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
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            
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
            function addPasswordToggle(field) {
                if (field) {
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
                    
                    const inputWrapper = field.parentElement;
                    inputWrapper.style.position = 'relative';
                    inputWrapper.appendChild(toggleBtn);
                    
                    toggleBtn.addEventListener('click', () => {
                        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                        field.setAttribute('type', type);
                        toggleBtn.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                    });
                }
            }

            addPasswordToggle(passwordField);
            addPasswordToggle(confirmPasswordField);

            // Real-time password confirmation validation
            if (confirmPasswordField) {
                confirmPasswordField.addEventListener('input', function() {
                    if (this.value !== passwordField.value) {
                        this.style.borderColor = '#ef4444';
                        this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
                    } else {
                        this.style.borderColor = 'var(--border-color)';
                        this.style.boxShadow = 'none';
                    }
                });
            }

            // Password strength indicator
            if (passwordField) {
                passwordField.addEventListener('input', function() {
                    const strength = getPasswordStrength(this.value);
                    const strengthBar = document.getElementById('password-strength');
                    if (strengthBar) {
                        strengthBar.style.width = strength + '%';
                        strengthBar.style.backgroundColor = getStrengthColor(strength);
                    }
                });
            }

            function getPasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength += 25;
                if (password.match(/[a-z]/)) strength += 25;
                if (password.match(/[A-Z]/)) strength += 25;
                if (password.match(/[0-9]/)) strength += 25;
                return strength;
            }

            function getStrengthColor(strength) {
                if (strength < 50) return '#ef4444';
                if (strength < 75) return '#f59e0b';
                return '#10b981';
            }
        });
    </script>
</body>
</html>
