<?php
// Sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Truncate text
function truncateText($text, $length = 150) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

// Convert markdown to HTML (basic implementation)
function markdownToHtml($text) {
    // Headers
    $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
    
    // Bold
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    // Italic
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
    
    // Line breaks
    $text = nl2br($text);
    
    return $text;
}
?>