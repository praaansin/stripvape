<?php
session_start();
include('connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
            width: 50%;
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
        .footer-text a {
            color: #238aea;
            text-decoration: none;
        }
        .footer-text a:hover {
            text-decoration: underline;
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
        <h2 class="title">Forgot Password</h2>
        
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['msg_type']; ?>">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="send_reset_link.php" method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <p class="footer-text">
            Remember your password? <a href="login.php">Login Here</a>
        </p>
    </div>
</body>
</html>