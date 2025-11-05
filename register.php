<?php
require_once 'includes/auth.php';
redirectIfLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - My Blog App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Create Account</h2>
            <p class="auth-subtitle">Join our blogging community</p>
            
            <div id="message" class="message"></div>
            
            <form id="registerForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small>At least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn-primary btn-block">Register</button>
            </form>
            
            <p class="auth-switch">
                Already have an account? <a href="login.php">Login here</a>
            </p>
            
            <a href="index.php" class="back-home">← Back to Home</a>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageDiv = document.getElementById('message');
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message + ' Redirecting to login...';
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = data.message;
                }
            } catch (error) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'An error occurred. Please try again.';
            }
        });
    </script>
</body>
</html>