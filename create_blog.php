<?php
require_once 'includes/auth.php';
requireLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog - My Blog App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>📝 My Blog</h2>
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="dashboard.php">Dashboard</a>
                <span class="user-info">Welcome, <?php echo getUsername(); ?>!</span>
                <a href="api/logout.php" class="btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <h1 class="page-title">Create New Blog Post</h1>
        
        <div id="message" class="message"></div>
        
        <form id="createBlogForm" class="blog-form">
            <div class="form-group">
                <label for="title">Blog Title</label>
                <input type="text" id="title" name="title" required placeholder="Enter your blog title">
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <div class="editor-toolbar">
                    <button type="button" onclick="insertMarkdown('**', '**')" title="Bold">B</button>
                    <button type="button" onclick="insertMarkdown('*', '*')" title="Italic">I</button>
                    <button type="button" onclick="insertMarkdown('# ', '')" title="Heading 1">H1</button>
                    <button type="button" onclick="insertMarkdown('## ', '')" title="Heading 2">H2</button>
                    <button type="button" onclick="insertMarkdown('### ', '')" title="Heading 3">H3</button>
                </div>
                <textarea id="content" name="content" rows="15" required placeholder="Write your blog content here... You can use Markdown formatting:
# Heading 1
## Heading 2
**bold text**
*italic text*"></textarea>
                <small>Supports Markdown formatting</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Publish Blog</button>
                <a href="dashboard.php" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 My Blog App. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function insertMarkdown(before, after) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selectedText = text.substring(start, end);
            
            const newText = text.substring(0, start) + before + selectedText + after + text.substring(end);
            textarea.value = newText;
            
            // Set cursor position
            const newPos = start + before.length + selectedText.length + after.length;
            textarea.setSelectionRange(newPos, newPos);
            textarea.focus();
        }

        document.getElementById('createBlogForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageDiv = document.getElementById('message');
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/create_blog.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message + ' Redirecting...';
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1500);
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