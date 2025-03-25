<?php
// Include database connection if needed (optional in this case)
require 'connection.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StripVape</title>
    <link href="https://fonts.cdnfonts.com/css/gloock-2" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
                
    <style>
        @import url('https://fonts.cdnfonts.com/css/gloock-2');
/* General Page Styling */
body {
    background-image: url('./assets/img/bg.jpg ');
    color: white;
    font-family: 'Gloock', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 95vh;
    flex-direction: column;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Logo and Text Wrapper */
.header-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 50px; /* Spacing between logo and text */
    margin-top: -2%;
    width: 80%;
    margin-right: 85px;
}

/* Logo Styling */
.logo {
    width: 280px; /* Adjust size as needed */
    margin-left: 8%;
}

/* Text Container */
.text-container {
    text-align: center; /* Align text to the left */
}
@font-face {
    font-family: 'Horizon';
    src: url('./assets/fonts/Horizon.otf') format('truetype'); /* Update the filename if needed */
    font-weight: normal;
    font-style: normal;
}
/* Main Title */
.title {
    font-size: 4.5rem;
    font-weight: 900;
    letter-spacing: 1.4px;
    margin-right: 60px;
    font-family: 'Horizon', sans-serif;
}

@font-face {
    font-family: 'League Spartan';
    src: url('./assets/fonts/LeagueSpartan.ttf') format('truetype');
    font-weight: 300;
    font-style: normal;
}

/* Subtitle */
.subtitle {
    font-size: 1.7em;
    font-weight: 900;
    font-family: 'League Spartan', sans-serif;
    margin-top: -35px;
    margin-left: -9%;
}

/* Call-to-Action Button */
.cta-button {
    background-color: #2588FF;
    position: relative;
    color: white;
    font-size: 1.2rem;
    font-weight: bold;
    padding: 12px 25px;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: 0.3s;
    margin-right: 25%;
    font-family: 'League Spartan', sans-serif;
    height: 50px;
    margin-top: 3%;
}

.cta-button:hover {
    background-color: #0f6edd;
}

/* Feature Boxes */
.features {
    display: flex;
    justify-content: center;
    gap: 50px;
    margin-top: 30px;
    font-family: 'League Spartan', sans-serif;
    margin-left: 12%;
    margin-top: 4%;
}

.feature-box {
    background-color: rgba(255, 255, 255, 0.2);
    padding: 15px;
    width: 250px;
    border-radius: 10px;
    text-align: center;
}

.feature-box h3 {
    font-size: 1.3rem;
    margin-bottom: 8px;
}

  .wrapper {
    background: linear-gradient(to right, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.8) 100%);
            padding: 40px;
            border-radius: 10px;
            width: 90%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

    </style>
</head>
<body>
<div class="wrapper">
        <div class="header-container">
            <img src="./assets/img/logo1.png" alt="StripVape Logo" class="logo">
            <div class="text-container">
                <h1 class="title">STRIPVAPE</h1>
                <p class="subtitle">Explore our latest collection of vaping products</p>
                <button class="cta-button" onclick="window.location.href='login.php'">GET STARTED</button>
            </div>
        </div>
       
        <div class="features">
            <div class="feature-box">
                <h3>Real-time</h3>
                <p>Real-time update to products and sales</p>
            </div>
            <div class="feature-box">
                <h3>Competitive Pricing</h3>
                <p>Our prices are competitive to give you the best value for your money.</p>
            </div>
            <div class="feature-box">
                <h3>Customer Support</h3>
                <p>Our customer support team is available 24/7 to assist you with any inquiries.</p>
            </div>
        </div>
    </div>
</body>
</html>