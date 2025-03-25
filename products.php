<?php
include('connection.php');

// Get the filter parameter from the URL
$filter = $_GET['filter'] ?? 'total_stock';

// Define the SQL query based on the filter
switch ($filter) {
    case 'out_of_stock':
        $sql = "SELECT * FROM products WHERE qty = 0";
        $title = "Out of Stock Products";
        break;
    case 'low_stock':
        $sql = "SELECT * FROM products WHERE qty > 0 AND qty <= 10";
        $title = "Low Stock Products";
        break;
    case 'high_stock':
        $sql = "SELECT * FROM products WHERE qty > 10 AND qty <= 50";
        $title = "High Stock Products";
        break;
    default:
        $sql = "SELECT * FROM products";
        $title = "All Products";
        break;
}

// Fetch products from the database
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
<title>Products</title>

<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/animate.css">
<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<  <div class="main-wrapper">
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

<a id="mobile_btn" class="mobile_btn" href="#sidebar">
<span class="bar-icon">
<span></span>
<span></span>
<span></span>
</span>
</a>

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
            </ul>
        </div>
    </div>
</div>

    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <div class="card">
<div class="card-body">
<div class="table-top">
<div class="search-set">
<div class="search-input">
<a class="btn btn-searchset"><img src="assets/img/icons/search-white.svg" alt="img"></a>
</div>
</div>
<div class="wordset">
<ul>
<li>
<a href="javascript:void(0);" onclick="printTable()" data-bs-toggle="tooltip" data-bs-placement="top" title="Print">
    <img src="assets/img/icons/printer.svg" alt="img">
</a>

</li>
</ul>
</div>
</div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive"> 
                        <table class="table">
                            <thead style="text-align: center;">
                                <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                    <th>Flavor</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php 
    $sno = 1; // Start the row count
    if (mysqli_num_rows($result) > 0): 
        while ($row = mysqli_fetch_assoc($result)): ?>
            <tr style="text-align: center;">
                <td><?= htmlspecialchars($row['id']); ?></td>
                <td style="text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?= htmlspecialchars($row['image']); ?>" width="40" height="40" style="border-radius: 5px;">
                        <?php else: ?>
                            <span>No Image</span>
                        <?php endif; ?>
                        <span style="text-align: left;"><?= htmlspecialchars($row['productname']); ?></span>
                    </div>
                </td>
                <td><?= htmlspecialchars($row['flavor']); ?></td>
                <td><?= htmlspecialchars($row['category_name']); ?></td>
                <td><?= htmlspecialchars($row['brand_name']); ?></td>
                <td>₱<?= number_format($row['price'], 2); ?></td>
                <td><?= htmlspecialchars($row['qty']); ?></td>
                <td style="text-align: center;">
                    <a href="editproduct.php?id=<?= $row['id']; ?>" style="color: #4CAF50; text-decoration: none; font-weight: bold; padding: 5px;">Edit</a> |  
                    <a href="productlist.php?delete_id=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');" style="color: #f44336; text-decoration: none; font-weight: bold; padding: 5px;">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8" class="text-center">No products found.</td></tr>
    <?php endif; ?>
</tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/js/jquery.slimscroll.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/dataTables.bootstrap4.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js"></script>
<script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
<script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>

<?php mysqli_close($conn); ?>