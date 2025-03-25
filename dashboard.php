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

// Fetch Monthly Sales
$currentMonth = date("m");
$query = "SELECT SUM(amount) AS monthlySales FROM sales WHERE YEAR(date) = ? AND MONTH(date) = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $selectedYear, $currentMonth);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$monthlySales = $row['monthlySales'] ?? 0;


$year = date("Y"); // Default to current year
if (isset($_GET['year'])) {
    $year = intval($_GET['year']); // Get selected year from dropdown
}

// Fetch total sales
$query = "SELECT SUM(amount) AS totalSales FROM sales WHERE YEAR(date) = $year";
$result = $conn->query($query);

$totalSales = 0; // Default value
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalSales = $row['totalSales'] ?? 0;
}
// Get the selected year (default to current year)
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

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
      background-color: #DBDEEF;
      display: flex;
      flex-direction: column;
      font-family: Arial, sans-serif;
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
    padding-top: 3%;
    font-family: 'Poppins', sans-serif !important;
    font-size: 45px;
    font-weight: 700;
    color: #0d0836 !important;
    padding-bottom: 1%;
}
.stock-report {
    position: absolute;
    right: 20px; /* Adjust this to match your layout */
    bottom: -5%;
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

/* Custom styles for the statistic blocks */
.statistic-block {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    transition: transform 0.2s ease-in-out;
}

.statistic-block:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
  </style>

</head>
<body>
    <div class="main-wrapper">
        <!-- Your existing layout goes here... -->
        <div class="header">

<div class="header-left active">
<a href="home.php" class="logo">
<img src="assets/img/logo2.png" alt="">
</a>
<a href="home.php" class="logo-small">
<img src="assets/img/logo-small.png" alt="">
</a>

</div>


<ul class="nav user-menu">

<li class="nav-item">
<div class="top-nav-search">
<a href="javascript:void(0);" class="responsive-search">
<i class="fa fa-search"></i>
</a>
<form action="#">
<div class="searchinputs">
<input type="text" placeholder="Search Here ...">
<div class="search-addon">
<span><img src="assets/img/icons/closes.svg" alt="img"></span>
</div>
</div>
<a class="btn" id="searchdiv"><img src="assets/img/icons/search.svg" alt="img"></a>
</form>
</div>
</li>






<li class="nav-item dropdown has-arrow main-drop">
<a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
<span class="user-img"><img src="assets/img/profiles/avator1.jpg" alt="">
<span class="status online"></span></span>
</a>
<div class="dropdown-menu menu-drop-user">
<div class="profilename">
<div class="profileset">
<span class="user-img"><img src="assets/img/profiles/avator1.jpg" alt="">
<span class="status online"></span></span>
<div class="profilesets">
<h6>Jess</h6>
<h5>Admin</h5>
</div>
</div>
<hr class="m-0">
<a class="dropdown-item" href="profile.html"> <i class="me-2" data-feather="user"></i> My Profile</a>
<a class="dropdown-item" href="generalsettings.html"><i class="me-2" data-feather="settings"></i>Settings</a>
<hr class="m-0">
<a class="dropdown-item logout pb-0" href="signin.html"><img src="assets/img/icons/log-out.svg" class="me-2" alt="img">Logout</a>
</div>
</div>
</li>
</ul>


<div class="dropdown mobile-user-menu">
<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="profile.html">My Profile</a>
<a class="dropdown-item" href="generalsettings.html">Settings</a>
<a class="dropdown-item" href="signin.html">Logout</a>
</div>
</div>

</div>


<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="active">
                    <a href="home.php"><img src="assets/img/icons/dashboard.svg" alt="img"><span> Dashboard</span> </a>
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
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="assets/img/icons/users1.svg" alt="img"><span> Users</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="newuser.php">New User </a></li>
                        <li><a href="userlists.php">Users List</a></li>
                    </ul>
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
                        <div class="icon"><i class="fas fa-check-circle"></i></div><strong>High Stock</strong>
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
        <h2><i class="bi bi-receipt"></i> Sales Report</h2>
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
        window.location.href = "home.php?year=" + selectedYear;
    });
    </script>


        <!-- Recently Added Products -->
        <div class="col-md-5">
            <div class="card shadow-sm p-3 added">
                <h2>Recently Added Products</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
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
                backgroundColor: 'rgba(0,128,128, 0.4)',
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
                    backgroundColor: 'rgba(0,134,173, 0.6)',
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
var monthlySales = <?php echo json_encode($monthlySales); ?>;

var dailyCtx = document.getElementById('dailyChart').getContext('2d');
var monthlyCtx = document.getElementById('monthlyChart').getContext('2d');

new Chart(dailyCtx, {
    type: 'doughnut',
    data: {
        labels: ["Sales", "Remaining"],
        datasets: [{
            data: [dailySales, dailySales * 0.2], // Adjust "remaining" data
            backgroundColor: ['#28a745', '#e9ecef'],
        }]
    }
});

new Chart(monthlyCtx, {
    type: 'doughnut',
    data: {
        labels: ["Sales", "Remaining"],
        datasets: [{
            data: [monthlySales, monthlySales * 0.2],
            backgroundColor: ['#007bff', '#e9ecef'],
        }]
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