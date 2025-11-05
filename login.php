<?php
require_once 'includes/auth.php';
redirectIfLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Blog App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Welcome Back</h2>
            <p class="auth-subtitle">Login to your account</p>
            
            <div id="message" class="message"></div>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-primary btn-block">Login</button>
            </form>
            
            <p class="auth-switch">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
            
            <a href="index.php" class="back-home">← Back to Home</a>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageDiv = document.getElementById('message');
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message + ' Redirecting...';
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
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