<?php
header('Content-Type: application/json');
include('connection.php');

$year = date("Y");
if (isset($_GET['year'])) {
    $year = intval($_GET['year']);
}

// Fetch Monthly Sales Data
$salesData = [];
$months = [];
for ($i = 1; $i <= 12; $i++) {
    $month = $year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    $months[] = date('F', strtotime($month . "-01"));
    
    $query = $conn->prepare("SELECT SUM(amount) AS total FROM sales WHERE YEAR(date) = ? AND MONTH(date) = ?");
    $query->bind_param("ii", $year, $i);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $salesData[] = $row['total'] ?? 0;
}

// Fetch Total Sales
$query = "SELECT SUM(amount) AS totalSales FROM sales WHERE YEAR(date) = $year";
$result = $conn->query($query);
$totalSales = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalSales = $row['totalSales'] ?? 0;
}

// Fetch Stock Data
$stockData = [];
$brandNames = [];
$stockQuery = $conn->query("SELECT brands.name AS brand_name, COALESCE(SUM(products.qty), 0) AS total_stock 
    FROM products 
    JOIN brands ON products.brand_id = brands.id 
    GROUP BY products.brand_id");

while ($row = $stockQuery->fetch_assoc()) {
    $brandNames[] = $row['brand_name'];
    $stockData[] = $row['total_stock'];
}

// Return JSON Response
echo json_encode([
    'months' => $months,
    'salesData' => $salesData,
    'totalSales' => $totalSales,
    'brandNames' => $brandNames,
    'stockData' => $stockData
]);
?>
