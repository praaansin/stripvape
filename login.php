<?php
session_start();
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Retrieve user from database
    $stmt = $conn->prepare("SELECT * FROM register_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];
        $user_id = $row['id']; // Get the user ID from the database row

        // Verify hashed password
        if (password_verify($password, $stored_password)) {
            $_SESSION['email'] = $email;
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['user_id'] = $user_id; // Store user ID in session

            // Set user status to active (1)
            $updateStatus = $conn->prepare("UPDATE register_user SET status = 1 WHERE id = ?");
            $updateStatus->bind_param("i", $user_id);
            $updateStatus->execute();

            // Redirect to home.php
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Invalid password.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email.'); window.location.href='login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=call" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* General Styling */
        body {
            background-image: url('./assets/img/bg.jpg ');
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-repeat: no-repeat;
            background-size: cover;
        }

        /* Container */
        .container {
            background-color: #f2efe7;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            width: 450px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.8);
            margin-bottom: 10%;
        }

        /* Logo */
        .logo img {
            width: 200px;
            border-radius: 50%;
        }
        @font-face {
    font-family: 'League Spartan';
    src: url('./assets/fonts/LeagueSpartan.ttf') format('truetype');
    font-weight: 300;
    font-style: normal;
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
            width:  30%;
            background-color: #444f54;
            color: #ebeae8;
            border: none;
            padding: 7px;
            font-size: 20px;
            border-radius: 24px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 3%;
            height: 40px;
            font-family: 'League Spartan';
        }

        .btn:hover {
            background-color: #1F2D3D;
            color:#EFEFEF;
        }

        /* Footer Text */
        .footer-text {
            margin-top: 15px;
            font-size: 16px;
            font-family: 'League Spartan';
        }
        .forgot-password {
            text-align: right;
            margin: 10px 0;
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

        /* Responsive */
        @media (max-width: 500px) {
            .input-container {
                grid-template-columns: 1fr;
            }
        }

        .terms {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: -1%;
    font-size: 17px;
    font-family: 'League Spartan';
}

.terms input {
    margin-right: 5px;
}

.terms a {
    color: #238aea;
    text-decoration: none;
}

.terms a:hover {
    text-decoration: underline;
}

#termsModal .modal-content {
    border-radius: 15px;
    overflow: hidden;
  }
  #termsModal .modal-body {
    background: #f8f9fa;
  }
  #termsModal a:hover {
    text-decoration: underline;
  }

    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="./assets/img/logo1.png" alt="Logo">
        </div>
        <h2 class="title">Login</h2>
        <form action="login.php" method="POST">
                <div class="input-container">

                <div class="input-group">
                    <span class="input-group-text" style="height:38px" id="basic-addon1"><i class="bi bi-envelope"></i></span> 
                    <input type="email" class="form-control mb-3" name="email" aria-describedby="basic-addon1" placeholder="Email:">
                </div>

                <div class="input-group">
                <span class="input-group-text" id="basic-addon1"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" name="password" aria-describedby="basic-addon1" placeholder="Password:">
                </div>
                <p class="forgot-password"><a href="forgetpassword.php" class="fs-6">Forgot your password?</a>
        </p>
                
    </div>
    
            <button type="submit" class="btn">Login</button>
        </form>
        
        <p class="footer-text">
            Don't have an account? <a href="register.php" class="fs-6">Register Here</a>
        </p>
        <hr>
        <div class="terms">
                <input type="checkbox" class="form-check-input" id="termsCheckbox" required>
                <label class="form-check-label  mt-2" for="termsCheckbox">
    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service and Policy</a>
</label>

    </div>

    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg rounded-4">
      <div class="modal-header bg-dark text-light">
        <h5 class="modal-title fw-bold" id="termsModalLabel">Terms of Service and Policy</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4" style="line-height: 1.8; color: #555;">
        <p><strong>Welcome to Stripvape!</strong> We are delighted to have you as a valued customer. Before proceeding with your purchases, we kindly ask you to take a moment to review our terms of service.</p>
        <p>By engaging with our services or making purchases on our platform, you affirm that you are at least <strong>18 years of age</strong>, in compliance with legal age requirements for the purchase of vape products.</p>
        <p>We prioritize the safety of our customers and encourage responsible usage of vaping devices and e-liquids. Please adhere to all manufacturer instructions for the proper handling, charging, and maintenance of your devices. Keep e-liquids out of reach of children and pets at all times.</p>
        <p>Should you receive a defective item or be dissatisfied with your purchase for any reason, we offer a <strong>14-day window</strong> for returns or exchanges. Simply contact our customer service team, and we will gladly assist you.</p>
        <p>We are committed to safeguarding your privacy and protecting your personal information. For details on how we collect, use, and protect your data, please refer to our <a href="#" class="text-decoration-none text-primary">privacy policy</a>.</p>
        <p class="fw-bold">Thank you for choosing Stripvape! We look forward to serving you.</p>
      </div>

      <div class="modal-footer justify-content-center gap-3">
        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">Agree</button>
        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script>

</script>
</body>
</html>