<?php  
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $category_name = mysqli_real_escape_string($conn, $_POST['name']);  
    $brand_id = (int) $_POST['brand_id'];

     // Fetch the brand name based on the selected brand_id
     $brandQuery = "SELECT name FROM brands WHERE id = $brand_id";
     $brandResult = mysqli_query($conn, $brandQuery);
     $brandRow = mysqli_fetch_assoc($brandResult);
     $brand_name = mysqli_real_escape_string($conn, $brandRow['name']);

    // Generate a unique category code based on name (e.g., first 3 letters + timestamp)
    $category_code = strtoupper(substr($category_name, 0, 3)) . time();

    // Check if category code already exists
    $checkQuery = "SELECT id FROM category WHERE category_code = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $category_code);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        die("Category code already exists. Try again.");
    }

    // Insert category into the database
    $sql = "INSERT INTO category (category_name, brand_id, category_code) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $category_name, $brand_id, $category_code);

    if ($stmt->execute()) {
        echo "Category added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

<title>Add Category</title>
<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/animate.css">
<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<style>
    select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #fff;
        color: #333;
        font-size: 14px;
        height: 40px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
    }
    
    select:focus {
        border-color: #4a90e2;
        outline: none;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
    }
    
    /* Style for number inputs */
    input[type="number"] {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        height: 40px;
    }
    
    input[type="number"]:focus {
        border-color: #4a90e2;
        outline: none;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
    }
    
    /* Style for file input */
    input[type="file"] {
        width: 100%;
        padding: 8px;
    }
    
    /* Input group styling */
    .input-group {
        display: flex;
        align-items: center;
    }
    
    .input-group-text {
        padding: 8px 12px;
        background-color: #f5f5f5;
        border: 1px solid #ddd;
        border-right: none;
        border-radius: 4px 0 0 4px;
        height: 40px;
    }
    
    .input-group input {
        border-radius: 0 4px 4px 0;
    }
</style>
</head>
<body>

<div class="main-wrapper">

<div class="header">

<div class="header-left active">
<a href="home.php" class="logo">
<img src="assets/img/logo2.png" alt="">
</a>
<a href="index.html" class="logo-small">
<img src="assets/img/logo-small.png" alt="">
</a>
<a id="toggle_btn" href="javascript:void(0);">
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
<h4>Product Add Category</h4>
</div>
</div>

<div class="card">
        <div class="card-body">
        <form action="addcategory.php" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" required>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Brand</label>
                <select name="brand_id" required>
                    <option value="">Select Brand</option>
                    <?php
                    // Fetch the available brands from the database
                    include('connection.php');
                    $brandQuery = $conn->query("SELECT id, name FROM brands");
                    while ($brand = $brandQuery->fetch_assoc()) {
                        echo "<option value='{$brand['id']}'>{$brand['name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>



        <div class="col-lg-12">
            <button type="submit" class="btn btn-submit">Submit</button>
            <a href="categorylist.php" class="btn btn-cancel">Cancel</a>
        </div>
    </div>
</form>

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