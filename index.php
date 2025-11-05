<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Get all blog posts
$sql = "SELECT b.*, u.username FROM blogpost b 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Home - My Blog App</title>
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
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="create_blog.php" class="btn-primary">Create Blog</a>
                    <span class="user-info">Welcome, <?php echo getUsername(); ?>!</span>
                    <a href="api/logout.php" class="btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-primary">Login</a>
                    <a href="register.php" class="btn-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <h1 class="page-title">Latest Blog Posts</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="blog-grid">
                <?php while($blog = $result->fetch_assoc()): ?>
                    <div class="blog-card">
                        <h3 class="blog-title">
                            <a href="view_blog.php?id=<?php echo $blog['id']; ?>">
                                <?php echo htmlspecialchars($blog['title']); ?>
                            </a>
                        </h3>
                        <div class="blog-meta">
                            <span class="author">By <?php echo htmlspecialchars($blog['username']); ?></span>
                            <span class="date"><?php echo formatDate($blog['created_at']); ?></span>
                        </div>
                        <div class="blog-excerpt">
                            <?php echo truncateText(strip_tags($blog['content']), 200); ?>
                        </div>
                        <a href="view_blog.php?id=<?php echo $blog['id']; ?>" class="read-more">Read More →</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-posts">
                <p>No blog posts yet. Be the first to create one!</p>
                <?php if (isLoggedIn()): ?>
                    <a href="create_blog.php" class="btn-primary">Create Your First Blog</a>
                <?php else: ?>
                    <a href="register.php" class="btn-primary">Register to Create Blogs</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 My Blog App. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>

<?php $conn->close(); ?>