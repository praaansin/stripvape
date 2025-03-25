<?php
session_start();
include('connection.php');

$token = $_GET['token'] ?? '';

// Verify token
$stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ? AND used = 0");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $reset = $result->fetch_assoc();
    $expires = strtotime($reset['expires_at']);
    $now = time();
    
    if ($now > $expires) {
        $_SESSION['message'] = "This password reset link has expired.";
        $_SESSION['msg_type'] = "danger";
        header("Location: forgot_password.php");
        exit();
    }
    
    $user_id = $reset['user_id'];
} else {
    $_SESSION['message'] = "Invalid password reset link.";
    $_SESSION['msg_type'] = "danger";
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
        $_SESSION['msg_type'] = "danger";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update user's password
        $stmt = $conn->prepare("UPDATE register_user SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();
        
        // Mark token as used
        $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        
        $_SESSION['message'] = "Your password has been reset successfully. You can now login with your new password.";
        $_SESSION['msg_type'] = "success";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #C7D9DD;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: #f2efe7;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            width: 450px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.8);
        }
        .logo img {
            width: 200px;
            border-radius: 50%;
        }
        .title {
            font-family: 'League Spartan';
            font-size: 31.7px;
            font-weight: bold;
            margin: 15px 0;
            color: #1F2D3D;
            margin-top: -8px;
            margin-bottom: 17px;
        }
        .btn {
            width: 30%;
            background-color: #444f54;
            color: #ebeae8;
            border: none;
            padding: 10px;
            font-size: 20px;
            border-radius: 24px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 3%;
            font-family: 'League Spartan';
        }
        .btn:hover {
            background-color: #1F2D3D;
            color:#EFEFEF;
        }
        .footer-text {
            margin-top: 15px;
            font-size: 16px;
            font-family: 'League Spartan';
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="./assets/img/logo1.png" alt="Logo">
        </div>
        <h2 class="title">Reset Password</h2>
        
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['msg_type']; ?>">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="reset_password.php?token=<?php echo $token; ?>" method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="password" placeholder="New Password" required>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm New Password" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
    </div>
</body>
</html>