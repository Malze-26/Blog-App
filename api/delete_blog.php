<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = intval($_POST['blog_id'] ?? 0);
    $user_id = getUserId();
    
    if ($blog_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid blog ID']);
        exit();
    }
    
    // Add retry logic for InfinityFree
    $max_retries = 3;
    $retry_count = 0;
    $success = false;
    
    while ($retry_count < $max_retries && !$success) {
        try {
            // Check if blog belongs to user - FIXED: use lowercase 'blogpost'
            $stmt = $conn->prepare("SELECT user_id FROM blogpost WHERE id = ?");
            
            if ($stmt === false) {
                sleep(1);
                $retry_count++;
                continue;
            }
            
            $stmt->bind_param("i", $blog_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Blog not found']);
                exit();
            }
            
            $blog = $result->fetch_assoc();
            if ($blog['user_id'] !== $user_id) {
                echo json_encode(['success' => false, 'message' => 'You can only delete your own blogs']);
                exit();
            }
            
            $stmt->close();
            
            // Small delay to avoid rate limit
            usleep(500000); // 0.5 seconds
            
            // Delete blog post - FIXED: use lowercase 'blogpost'
            $stmt = $conn->prepare("DELETE FROM blogpost WHERE id = ?");
            
            if ($stmt === false) {
                sleep(1);
                $retry_count++;
                continue;
            }
            
            $stmt->bind_param("i", $blog_id);
            
            if ($stmt->execute()) {
                $success = true;
                echo json_encode(['success' => true, 'message' => 'Blog deleted successfully']);
            } else {
                if (strpos($stmt->error, 'has gone away') !== false || 
                    strpos($stmt->error, 'Lost connection') !== false) {
                    $retry_count++;
                    sleep(1);
                    
                    $conn->close();
                    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                    continue;
                }
                
                throw new Exception('Database error');
            }
            
            $stmt->close();
            
        } catch (Exception $e) {
            $retry_count++;
            if ($retry_count >= $max_retries) {
                echo json_encode(['success' => false, 'message' => 'Failed to delete blog. Please try again.']);
            } else {
                sleep(1);
            }
        }
    }
    
    if (!$success && $retry_count >= $max_retries) {
        echo json_encode(['success' => false, 'message' => 'Server is busy. Please wait and try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>