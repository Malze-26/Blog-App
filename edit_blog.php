<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();

$blog_id = intval($_GET['id'] ?? 0);
$user_id = getUserId();

// Get blog post - FIXED: use lowercase 'blogpost'
$stmt = $conn->prepare("SELECT * FROM blogpost WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: dashboard.php');
    exit();
}

$blog = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - My Blog App</title>
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
        <h1 class="page-title">Edit Blog Post</h1>
        
        <div style="background: linear-gradient(135deg, #fff5e6 0%, #ffe6cc 100%); 
                    padding: 1rem; 
                    border-radius: 15px; 
                    margin-bottom: 1.5rem;
                    border: 2px solid #ffcc99;">
            <strong>ℹ️ Note:</strong> If you see an error, please wait 3 seconds and click "Update Blog" again.
        </div>
        
        <div id="message" class="message"></div>
        
        <form id="editBlogForm" class="blog-form">
            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
            
            <div class="form-group">
                <label for="title">Blog Title</label>
                <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($blog['title']); ?>">
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
                <textarea id="content" name="content" rows="15" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                <small>Supports Markdown formatting</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Blog</button>
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
            
            const newPos = start + before.length + selectedText.length + after.length;
            textarea.setSelectionRange(newPos, newPos);
            textarea.focus();
        }

        document.getElementById('editBlogForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageDiv = document.getElementById('message');
            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
            messageDiv.className = 'message';
            messageDiv.textContent = 'Updating your blog post, please wait...';
            messageDiv.style.display = 'block';
            messageDiv.style.background = 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)';
            messageDiv.style.color = '#333';
            
            try {
                const response = await fetch('api/update_blog.php', {
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
                    
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Update Blog';
                    
                    if (data.message.includes('busy') || data.message.includes('wait')) {
                        messageDiv.textContent += ' Please wait 3 seconds and try again.';
                    }
                }
            } catch (error) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'An error occurred. Please try again.';
                
                submitBtn.disabled = false;
                submitBtn.textContent = 'Update Blog';
            }
        });
    </script>
</body>
</html>

<?php 
$stmt->close();
$conn->close(); 
?>