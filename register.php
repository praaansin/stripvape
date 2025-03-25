<?php
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "try";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize input data
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $phone_no = $conn->real_escape_string($_POST['phone_no']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Validate phone number format
    if (!preg_match('/^\+63\d{10}$/', $phone_no)) {
        die("Phone number must be in the format +63 followed by 10 digits (total 13 characters)");
    }

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO register_user (firstname, lastname, phone_no, email, password)
            VALUES ('$firstname', '$lastname', '$phone_no', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=call" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined">
    <style>
        /* General Styling */
        body {
            background-image: url('./assets/img/bg.jpg ');
            display: flex;
            justify-content: center;
            align-items: center;
            height: 95vh;
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
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
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

        /* Title */
        .title {
            font-family: 'League Spartan';
            font-size: 31.7px;
            font-weight: bold;
            margin: 15px 0;
            color: #1F2D3D;
            margin-top: -8px;
            margin-bottom: 17px;
        }

        /* Input Fields - Two Column Layout */
        .input-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .input-group {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 15px;
            padding: 10px;
            border: 1px solid #000;
            width: 80%;
            margin-top: 5px;
        }

        .input-group input::placeholder {
    font-size: 14.7px; /* Adjust the size as needed */
    color: #555; /* Optional: Change placeholder color */
    font-family: 'Poppins', sans-serif;
}

        .input-group i {
            margin-right: 10px;
            color: #555;
            font-size: 18px;
        }

        .input-group input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 16px;
            background: none;
        }

        .input-group .material-icons-outlined {
    font-size: 21px; /* Adjust icon size */
    color: #555; /* Icon color */
    margin-right: 10px; /* Space between icon and input */
}

        /* Button */
        .btn {
            width:  50%;
            background-color: #444f54;
            color: #ebeae8;
            border: none;
            padding: 12px;
            font-size: 22px;
            border-radius: 22px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
            font-family: 'League Spartan';
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #1F2D3D;
        }

        /* Footer Text */
        .footer-text {
            margin-top: 15px;
            font-size: 14px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="./assets/img/logo1.png" alt="Logo">
        </div>
        <h2 class="title">Registration</h2>
        <form action="register.php" method="POST">
            <div class="input-container">
                <!-- Left Side -->
                <div class="input-group">
                    <i class="far fa-user"></i>
                    <input type="text" name="firstname" placeholder="First Name:">
                </div>
                <div class="input-group">
                    <i class="far fa-user"></i>
                    <input type="text" name="lastname" placeholder="Last Name:">
                </div>
                <div class="input-group">
                    <i class="far fa-id-badge"></i>
                    <input type="tel" name="phone_no" placeholder="Phone No:" value="+63" 
                           pattern="\+63\d{10}" 
                           maxlength="13" 
                           title="Phone number must be +63 followed by 10 digits" required>
                </div>
                <div class="input-group">
                    <i class="far fa-envelope"></i> 
                    <input type="email" name="email" placeholder="Email:">
                </div>
                <div class="input-group">
                    <span class="material-icons-outlined">lock</span>
                    <input type="password" name="password" placeholder="Password:">
                </div>
                <div class="input-group">
                    <span class="material-icons-outlined">lock</span>
                    <input type="password" name="password" placeholder="Confirm Password:">
                </div>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <p class="footer-text">
            Already have an account? <a href="login.php">Login Here</a>
        </p>
    </div>

    <script>
        // JavaScript to ensure proper phone number format
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.querySelector('input[name="phone_no"]');
            
            // Ensure the +63 prefix is always present
            phoneInput.addEventListener('input', function(e) {
                if (!this.value.startsWith('+63')) {
                    this.value = '+63' + this.value.replace(/^\+63/, '');
                }
                
                // Limit to 13 characters (+63 + 10 digits)
                if (this.value.length > 13) {
                    this.value = this.value.slice(0, 13);
                }
            });
            
            // Prevent backspace from deleting the +63 prefix
            phoneInput.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length <= 3) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>