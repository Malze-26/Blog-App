<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$blog_id = intval($_GET['id'] ?? 0);

// Get blog post with author info
$stmt = $conn->prepare("SELECT b.*, u.username FROM blogpost b 
                       JOIN users u ON b.user_id = u.id 
                       WHERE b.id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$blog = $result->fetch_assoc();
$isOwner = isLoggedIn() && getUserId() === $blog['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - My Blog App</title>
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
        <article class="blog-view">
            <header class="blog-header">
                <h1 class="blog-view-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
                <div class="blog-view-meta">
                    <span class="author">By <?php echo htmlspecialchars($blog['username']); ?></span>
                    <span class="date">Published on <?php echo formatDate($blog['created_at']); ?></span>
                    <?php if ($blog['updated_at'] !== $blog['created_at']): ?>
                        <span class="updated">Updated on <?php echo formatDate($blog['updated_at']); ?></span>
                    <?php endif; ?>
                </div>
            </header>

            <?php if ($isOwner): ?>
                <div class="blog-actions">
                    <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn-edit">Edit Blog</a>
                    <button onclick="deleteBlog(<?php echo $blog['id']; ?>)" class="btn-delete">Delete Blog</button>
                </div>
            <?php endif; ?>

            <div class="blog-content">
                <?php echo markdownToHtml($blog['content']); ?>
            </div>
        </article>

        <div class="blog-navigation">
            <a href="index.php" class="btn-secondary">← Back to Home</a>
            <?php if ($isOwner): ?>
                <a href="dashboard.php" class="btn-secondary">Go to Dashboard</a>
            <?php endif; ?>
        </div>
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