<?php
session_start();
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM register_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Generate unique token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expires in 1 hour
        
        // Store token in database
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $token, $expires);
        $stmt->execute();
        
        // Send email with reset link
        $reset_link = "http://yourdomain.com/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Hello,\n\nYou have requested to reset your password. Click the following link to reset your password:\n\n$reset_link\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.";
        $headers = "From: no-reply@yourdomain.com";
        
        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['message'] = "Password reset link has been sent to your email.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to send reset link. Please try again.";
            $_SESSION['msg_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "No account found with that email address.";
        $_SESSION['msg_type'] = "danger";
    }
    
    header("Location: forgetpassword.php");
    exit();
}
?>