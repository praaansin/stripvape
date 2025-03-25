<?php 
include('connection.php');

// Fetch data from the products table
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Fetch Daily Sales
$today = date("Y-m-d");
$query = "SELECT SUM(amount) AS dailySales FROM sales WHERE date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$dailySales = $row['dailySales'] ?? 0;

// Set daily sales target (you can modify this value as needed)
$dailyTarget = 5000; // Example daily target of ₱5,000

// Get the selected year (default to current year)
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Get the selected month (default to current month)
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');

// Fetch Monthly Sales for selected month and year
$query = "SELECT SUM(amount) AS monthlySales FROM sales WHERE YEAR(date) = ? AND MONTH(date) = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $selectedYear, $selectedMonth);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$monthlySales = $row['monthlySales'] ?? 0;

// Set monthly sales target (you can modify this value as needed)
$monthlyTarget = 50000; // Example monthly target of ₱150,000

// Fetch total sales for selected year
$query = "SELECT SUM(amount) AS totalSales FROM sales WHERE YEAR(date) = $selectedYear";
$result = $conn->query($query);

$totalSales = 0; // Default value
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalSales = $row['totalSales'] ?? 0;
}

// Prepare data for the yearly sales chart
$salesData = [];
$months = [];

for ($i = 1; $i <= 12; $i++) { 
    $month = $selectedYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    $months[] = date('F', strtotime($month . "-01"));

    $query = $conn->prepare("SELECT SUM(amount) AS total FROM sales WHERE YEAR(date) = ? AND MONTH(date) = ?");
    $query->bind_param("ii", $selectedYear, $i);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $salesData[] = $row['total'] ?? 0;
}

// Check if there is any sales data
$hasData = array_sum($salesData) > 0;

// Fetch Stock Data
$stockData = [];
$brandNames = [];
$stockQuery = $conn->query("SELECT brands.name AS brand_name, COALESCE(SUM(products.qty), 0) AS total_stock 
    FROM products 
    JOIN brands ON products.brand_id = brands.id 
    GROUP BY products.brand_id
");

while ($row = $stockQuery->fetch_assoc()) {
    $brandNames[] = $row['brand_name'];
    $stockData[] = $row['total_stock'];
}

// Fetch Recently Added Products
$productsQuery = $conn->query("SELECT products.*, brands.name AS brand_name 
    FROM products 
    LEFT JOIN brands ON products.brand_id = brands.id 
    ORDER BY products.id DESC LIMIT 10
");

// Fetch stock data for the status containers
$stockStatusQuery = $conn->query("
    SELECT 
        SUM(qty) AS total_stock,
        SUM(CASE WHEN qty = 0 THEN 1 ELSE 0 END) AS out_of_stock,
        SUM(CASE WHEN qty > 0 AND qty <= 10 THEN 1 ELSE 0 END) AS low_stock,
        SUM(CASE WHEN qty > 10 AND qty <= 50 THEN 1 ELSE 0 END) AS high_stock
    FROM products
");

$stockStatusData = $stockStatusQuery->fetch_assoc();

// Fetch data from the products table with brand name
$sql = "SELECT products.*, brands.name AS brand_name 
        FROM products 
        LEFT JOIN brands ON products.brand_id = brands.id 
        ORDER BY products.id DESC LIMIT 10";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching products: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>StripVape</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
    body{
      display: flex;
      flex-direction: column;
      font-family: Arial, sans-serif;
    }

    .main-wrapper{
        min-height: 110vh;
    }

    .status-badge {
      border-radius: 20px;
      padding: 5px 10px;
      font-size: 12px;
    }
    .content-area {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .status-card {
    height: 200px; /* Adjust as needed */
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.status-card canvas {
    max-height: 150px; /* Reduce the chart height */
}

    .status-card h6 {
      margin: 0;
      font-size: 14px;
      color: #333;
    }

    .status-percentage {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 25px;
      color: #000;
    }

    .circle-container {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
    }
    .dashboard-container {
    display: flex;
    justify-content: space-between;
    gap: 50px;
}

.left-section {
    flex: 4;
}

.right-section {
    flex: 1;
}
.container {
    width: 100%;
    max-width: 100%;
    margin-left: 16%;
}

.circle-container {
    display: flex;
    flex-wrap: nowrap; /* Ensures they are in a single row */
    gap: 15px;
    overflow-x: auto; /* In case it overflows */
    padding-bottom: 10px;
}

.table-responsive {
    max-height: 250px; /* Adjust as needed */
    overflow-y: auto;
}
.table-responsive::-webkit-scrollbar-thumb {
    background: #aaaaaa; /* Green */
    border-radius: 5px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #ddd; /* Light grey */
}

.status-card {
    flex: 1;
    min-width: 180px;
    text-align: center;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.sales-report h2,
.stock-report h2 {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 500;
    font-size: 24px;
}

.added h2{
    font-family: 'Poppins', sans-serif !important;
    font-weight: 500;
    font-size: 26px;
}

.sales-amount {
    font-family: 'Poppins', sans-serif !important;
    font-size: 30px;
    font-weight: bold;
    color: rgb(25 135 84);
}
.text-muted{
    padding-top: 3.4%;
    font-family: 'Poppins', sans-serif !important;
    font-size: 28px;
    font-weight: 700;
    color: #0d0836 !important;
    padding-bottom: -1%;
}
.stock-report {
    position: absolute;
    right: 25px; /* Adjust this to match your layout */
    bottom: -10%;
    padding: 10px; /* Reduce padding */
    width: 40%;
}

.sales-report {
    position: relative;
}

#yearSelect {
    position: absolute;
    top: 5%;
    right: 20px; /* Adjust as needed */
    width: auto;
}

/* Custom styles for icons */
.icon {
    font-size: 24px;
    color: #333;
    margin-right: 10px;
}

/* Total Stock */
.col-md-3:nth-of-type(1) .icon i {
    color: #A08963;
}

/* Out of Stock */
.col-md-3:nth-of-type(2) .icon i {
    color: #e74c3c;
}

/* Low Stock */
.col-md-3:nth-of-type(3) .icon i {
    color: #f39c12;
}

/* Overstocked */
.col-md-3:nth-of-type(4) .icon i {
    color: #2ecc71;
}

/* Custom styles for the statistic blocks */
.statistic-block {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    transition: transform 0.2s ease-in-out;
}

.statistic-block:hover {
    transform: translateY(-7px);
    box-shadow: 0 5px 8px rgba(0, 0, 0, 0.5);
}

.statistic-block .title {
    font-size: 16px;
    color: #666;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-weight: 600;
}

.statistic-block .number {
    font-size: 32px;
    font-weight: bold;
    color: #333;
}  
/* Sidebar Logout Button */
.sidebar-logout {
    margin-top: 250%; /* Pushes it to the bottom */
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    position: sticky;
    bottom: 0;
    background-color: #E52020;
    border-radius: 5px;
    height: 45px;
    width: 180px;
    margin-left: 15px;
    border: none;
}

.sidebar-logout a {
    color: white !important;
    padding: 12px 15px;
    display: flex;
    align-items: center;
    justify-content: center; /* Center horizontally */
    text-align: center; /* Center text */
    transition: all 0.3s;
    height: 45px;
    border-radius: 5px;
}

.sidebar-logout a:hover {
    background-color: #c82333;
    text-decoration: none;
    height: 45px;
}

.sidebar-logout i {
    margin-right: 10px;
    font-size: 18px;
}

.sidebar-logout span {
    font-weight: bold !important;
    color: white !important;
    font-size: 1rem !important;
    text-transform: uppercase;
}

/* Ensure the sidebar has proper flex layout */
.sidebar-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    height: 100%;
}
  </style>

</head>
<body>
    <div class="main-wrapper">
        <!-- Your existing layout goes here... -->
        <div class="header">

<div class="header-left active">
<a href="index.php" class="logo">
<img src="assets/img/logo2.png" alt="">
</a>
<a href="index.php" class="logo-small">
<img src="assets/img/logo-small.png" alt="">
</a>

</div>


<ul class="nav user-menu">

</div>


<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="active">
                    <a href="index.php"><img src="assets/img/icons/dashboard.svg" alt="img"><span> Dashboard</span> </a>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="assets/img/icons/product.svg" alt="img"><span> Product</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="productlist.php">Product List</a></li>
                        <li><a href="addproduct.php">Add Product</a></li>
                        <li><a href="categorylist.php">Category List</a></li>
                        <li><a href="addcategory.php">Add Category</a></li>
                        <li><a href="brandlist.php">Brand List</a></li>
                        <li><a href="addbrand.php">Add Brand</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="javascript:void(0);"><img src="assets/img/icons/time.svg" alt="img"><span> Report</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="inventoryreport.php">Inventory Report</a></li>
                        <li><a href="salesreport.php">Sales Report</a></li>
                        <li><a href="addsales.php">Add New Sales</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="assets/img/icons/users1.svg" alt="img"><span> Users</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="userlists.php">Users List</a></li>
                    </ul>
                </li>
                <li class="sidebar-logout">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="main-wrapper">
        <div class="container mt-3">
        <h1 class="text-muted">Dashboard</h1>
        <div class="row">
        <div class="row mt-3">
        <div class="col-md-10">
        <div class="row">
  <!-- Total Stock -->
  <div class="col-md-3 col-sm-6">
        <a href="products.php?filter=total_stock" style="text-decoration: none;">
            <div class="statistic-block block">
                <div class="progress-details d-flex align-items-end justify-content-between">
                    <div class="title">
                        <div class="icon"><i class="fas fa-box"></i></div><strong>Total Stock</strong>
                    </div>
                    <div class="number dashtext-1"><?php echo $stockStatusData['total_stock']; ?></div>
                </div>
            </div>
        </a>
    </div>

    <!-- Out of Stock -->
    <div class="col-md-3 col-sm-6">
        <a href="products.php?filter=out_of_stock" style="text-decoration: none;">
            <div class="statistic-block block">
                <div class="progress-details d-flex align-items-end justify-content-between">
                    <div class="title">
                        <div class="icon"><i class="fas fa-times-circle"></i></div><strong>Out of Stock</strong>
                    </div>
                    <div class="number dashtext-2"><?php echo $stockStatusData['out_of_stock']; ?></div>
                </div>
            </div>
        </a>
    </div>

    <!-- Low Stock -->
    <div class="col-md-3 col-sm-6">
        <a href="products.php?filter=low_stock" style="text-decoration: none;">
            <div class="statistic-block block">
                <div class="progress-details d-flex align-items-end justify-content-between">
                    <div class="title">
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div><strong>Low Stock</strong>
                    </div>
                    <div class="number dashtext-3"><?php echo $stockStatusData['low_stock']; ?></div>
                </div>
            </div>
        </a>
    </div>

    <!-- High Stock -->
    <div class="col-md-3 col-sm-6">
        <a href="products.php?filter=high_stock" style="text-decoration: none;">
            <div class="statistic-block block">
                <div class="progress-details d-flex align-items-end justify-content-between">
                    <div class="title">
                        <div class="icon"><i class="fas fa-check-circle"></i></div><strong>Overstocked</strong>
                    </div>
                    <div class="number dashtext-4"><?php echo $stockStatusData['high_stock']; ?></div>
                </div>
            </div>
        </a>
    </div>
</div>
</div>
</div>

<div class="mt-4"></div>
  
<!-- Sales Chart -->
<div class="col-md-5">
    <div class="card shadow-sm p-3 sales-report">
        <h2><i class="bi bi-receipt"></i> Sales Chart</h2>
        <h3 class="sales-amount">₱ <?php echo number_format($totalSales, 2); ?></h3>
        <div>
    <select id="yearSelect" class="form-select form-select-sm w-auto">
        <?php 
        $years = range(2022, 2025); // Define the year range
        foreach ($years as $yearOption): ?>
            <option value="<?php echo $yearOption; ?>" <?php echo ($yearOption == $selectedYear) ? 'selected' : ''; ?>>
                <?php echo $yearOption; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

        <?php if ($hasData): ?>
    <canvas id="salesChart"></canvas>
<?php else: ?>
    <p class="text-center text-muted">No sales data available for <?php echo $selectedYear; ?>.</p>
<?php endif; ?>
    </div>
</div>

<script>
    document.getElementById("yearSelect").addEventListener("change", function () {
        var selectedYear = this.value;
        window.location.href = "index.php?year=" + selectedYear;
    });
    </script>


        <!-- Recently Added Products -->
<div class="col-md-5">
    <div class="card shadow-sm p-3 added">
        <h2>Recently Added Products</h2>
        <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
            <table class="table">
                <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                    <tr>
                        <th>Sno</th>
                        <th>Product Name</th>
                        <th>Brand</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sno = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $sno++ . "</td>
                            <td>" . $row['productname'] . "</td>
                            <td>" . $row['brand_name'] . "</td>
                            <td>₱" . number_format($row['price'], 2) . "</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Status Cards & Stock Report -->
    <div class="row mt-3">
        <!-- Status Cards -->
        <div class="col-md-5 d-flex">
            <div class="status-card me-3">
                <h6 class="text-muted">Daily Sales</h6>
                <canvas id="dailyChart"></canvas>
            </div>
            <div class="status-card">
                <h6 class="text-muted">Monthly Sales</h6>
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Stock Report -->
        <div class="col-md-5">
            <div class="card shadow-sm p-3 stock-report">
                <h2><i class="bi bi-box-seam"></i> Stock Report</h2>
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>
</div>
        <script>
  var salesCtx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Monthly Sales',
                data: <?php echo json_encode($salesData); ?>,
                borderColor: 'rgba(0,102,102)',
                backgroundColor: 'rgb(22, 196, 127, 0.4)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: "'Poppins', sans-serif",
                            size: 15.5
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: {
                            family: "'Poppins', sans-serif",
                            size: 12.5,
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    ticks: {
                        font: {
                            family: "'Poppins', sans-serif",
                            size: 12,
                        }
                    }
                }
            }
        }
    });

        var stockCtx = document.getElementById('stockChart').getContext('2d');
        var stockChart = new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($brandNames); ?>,
                datasets: [{
                    label: 'Stock Levels',
                    data: <?php echo json_encode($stockData); ?>,
                    backgroundColor: 'rgb(152, 216, 239, 0.6)',
                    borderColor: 'rgba(3,57,108)',
                    borderWidth: 2
                }]
            },
            options: {
        responsive: true,
        plugins: {
            legend: {
                labels: {
                    font: {
                        family: "'Poppins', sans-serif", 
                        size: 15.5,
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 13,
                        weight: 'bold'
                    }
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 10,
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12,
                    }
                }
            }
        }
    }
});

        function updateCharts() {
            fetch('get_chart_data.php')
                .then(response => response.json())
                .then(data => {
                    salesChart.data.labels = data.sales.labels;
                    salesChart.data.datasets[0].data = data.sales.values;
                    salesChart.update();

                    stockChart.data.labels = data.stock.labels;
                    stockChart.data.datasets[0].data = data.stock.values;
                    stockChart.update();
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }

        setInterval(updateCharts, 60000); // Auto-refresh every 60 seconds
            // Initialize chart on page load
    window.onload = function() {
        updateChart();  // Call to update the chart with initial data
    };

    var dailySales = <?php echo json_encode($dailySales); ?>;
    var dailyTarget = <?php echo $dailyTarget; ?>;
    var monthlySales = <?php echo json_encode($monthlySales); ?>;
    var monthlyTarget = <?php echo $monthlyTarget; ?>;

    var dailyCtx = document.getElementById('dailyChart').getContext('2d');
    var monthlyCtx = document.getElementById('monthlyChart').getContext('2d');

    new Chart(dailyCtx, {
        type: 'doughnut',
        data: {
            labels: ["Sales Achieved", "Sales Target"],
            datasets: [{
                data: [dailySales, dailyTarget],
                backgroundColor: ['#57B4BA', '#A2C579'],
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: "'Poppins', sans-serif",
                            size: 12
                        }
                    }
                }
            }
        }
    });

    new Chart(monthlyCtx, {
        type: 'doughnut',
        data: {
            labels: ["Sales Achieved", "Sales Target"],
            datasets: [{
                data: [monthlySales, monthlyTarget],
                backgroundColor: ['#070739', '#FFF6B3'],
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: "'Poppins', sans-serif",
                            size: 12
                        }
                    }
                }
            }
        }
    });

    </script>

    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script src="assets/plugins/apexchart/chart-data.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>