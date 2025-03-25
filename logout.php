<?php
session_start();
include('connection.php');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $update_sql = "UPDATE register_user SET status = 0 WHERE id = $user_id";
    mysqli_query($conn, $update_sql);
}

session_destroy();
header("Location: login.php");
exit();
?>