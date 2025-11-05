<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = getUserId();

// Get user's blog posts
$stmt = $conn->prepare("SELECT * FROM blogpost WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - My Blog App</title>
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
                <a href="create_blog.php" class="btn-primary">Create Blog</a>
                <span class="user-info">Welcome, <?php echo getUsername(); ?>!</span>
                <a href="api/logout.php" class="btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <h1 class="page-title">My Dashboard</h1>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3><?php echo $result->num_rows; ?></h3>
                <p>Total Posts</p>
            </div>
        </div>

        <div class="dashboard-actions">
            <a href="create_blog.php" class="btn-primary">✏️ Create New Blog</a>
        </div>

        <h2 class="section-title">My Blog Posts</h2>
        
        <div id="message" class="message"></div>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="blog-list">
                <?php while($blog = $result->fetch_assoc()): ?>
                    <div class="blog-item">
                        <div class="blog-item-content">
                            <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                            <p class="blog-excerpt">
                                <?php echo truncateText(strip_tags($blog['content']), 150); ?>
                            </p>
                            <div class="blog-meta">
                                <span>Created: <?php echo formatDate($blog['created_at']); ?></span>
                                <?php if ($blog['updated_at'] !== $blog['created_at']): ?>
                                    <span>Updated: <?php echo formatDate($blog['updated_at']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="blog-item-actions">
                            <a href="view_blog.php?id=<?php echo $blog['id']; ?>" class="btn-view">View</a>
                            <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn-edit">Edit</a>
                            <button onclick="deleteBlog(<?php echo $blog['id']; ?>)" class="btn-delete">Delete</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-posts">
                <p>You haven't created any blog posts yet.</p>
                <a href="create_blog.php" class="btn-primary">Create Your First Blog</a>
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

<?php 
$stmt->close();
$conn->close(); 
?>