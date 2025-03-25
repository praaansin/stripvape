<?php
include 'connection.php'; // Your database connection file

$year = isset($_GET['year']) ? $_GET['year'] : date("Y");

$query = "SELECT month, sales FROM sales_data WHERE year = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
