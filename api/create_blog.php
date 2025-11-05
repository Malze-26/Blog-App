<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $user_id = getUserId();
    
    // Validation
    if (empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Title and content are required']);
        exit();
    }
    
    // Add retry logic for InfinityFree MySQL rate limits
    $max_retries = 3;
    $retry_count = 0;
    $success = false;
    
    while ($retry_count < $max_retries && !$success) {
        try {
            // Insert blog post
            $stmt = $conn->prepare("INSERT INTO blogpost (user_id, title, content) VALUES (?, ?, ?)");
            
            if ($stmt === false) {
                // Wait a bit before retry
                sleep(1);
                $retry_count++;
                continue;
            }
            
            $stmt->bind_param("iss", $user_id, $title, $content);
            
            if ($stmt->execute()) {
                $success = true;
                echo json_encode(['success' => true, 'message' => 'Blog created successfully']);
            } else {
                // Check if it's a connection error
                if (strpos($stmt->error, 'has gone away') !== false || 
                    strpos($stmt->error, 'Lost connection') !== false) {
                    $retry_count++;
                    sleep(1);
                    
                    // Reconnect to database
                    $conn->close();
                    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                    continue;
                }
                
                throw new Exception('Database error: ' . $stmt->error);
            }
            
            $stmt->close();
            
        } catch (Exception $e) {
            $retry_count++;
            if ($retry_count >= $max_retries) {
                echo json_encode(['success' => false, 'message' => 'Failed to create blog. Please try again in a few seconds.']);
            } else {
                sleep(1);
            }
        }
    }
    
    if (!$success && $retry_count >= $max_retries) {
        echo json_encode(['success' => false, 'message' => 'Server is busy. Please wait a moment and try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>