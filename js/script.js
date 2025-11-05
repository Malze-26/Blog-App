// Delete blog function
async function deleteBlog(blogId) {
    if (!confirm('Are you sure you want to delete this blog? This action cannot be undone.')) {
        return;
    }

    const messageDiv = document.getElementById('message');
    
    // Show loading message
    if (messageDiv) {
        messageDiv.className = 'message';
        messageDiv.textContent = 'Deleting blog, please wait...';
        messageDiv.style.display = 'block';
        messageDiv.style.background = 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)';
        messageDiv.style.color = '#333';
    }
    
    try {
        const formData = new FormData();
        formData.append('blog_id', blogId);
        
        const response = await fetch('api/delete_blog.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (messageDiv) {
                messageDiv.className = 'message success';
                messageDiv.textContent = data.message + ' Redirecting...';
            }
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1500);
        } else {
            if (messageDiv) {
                messageDiv.className = 'message error';
                messageDiv.textContent = data.message;
                
                // If server busy, suggest retry
                if (data.message.includes('busy') || data.message.includes('wait')) {
                    messageDiv.textContent += ' Please wait 3 seconds and try clicking Delete again.';
                }
            } else {
                alert(data.message + (data.message.includes('busy') ? ' Please wait and try again.' : ''));
            }
        }
    } catch (error) {
        if (messageDiv) {
            messageDiv.className = 'message error';
            messageDiv.textContent = 'An error occurred while deleting the blog. Please try again.';
        } else {
            alert('An error occurred while deleting the blog. Please try again.');
        }
    }
}

// Auto-hide messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.message.success, .message.error');
    messages.forEach(message => {
        if (message.textContent.trim()) {
            setTimeout(() => {
                message.style.display = 'none';
            }, 5000);
        }
    });
});

// Form validation helper
function validateForm(formElement) {
    const inputs = formElement.querySelectorAll('input[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = '#e74c3c';
        } else {
            input.style.borderColor = '#ddd';
        }
    });

    return isValid;
}

// Add input event listeners to remove error styling
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.style.borderColor = '#ddd';
            }
        });
    });
});