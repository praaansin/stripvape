<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE register_user SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $status, $user_id);
    
    if ($stmt->execute()) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status";
    }
    
    $stmt->close();
}
$conn->close();
?>