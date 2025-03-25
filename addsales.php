<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $brand_id = (int) $_POST['brand_id']; // Ensure brand_id is an integer
    $sold_qty = (int) $_POST['quantity']; // Ensure sold_qty is an integer
    $amount = (float) $_POST['amount']; // Ensure amount is a float

    // Fetch the brand name based on the selected brand_id
    $brandQuery = "SELECT name FROM brands WHERE id = $brand_id";
    $brandResult = mysqli_query($conn, $brandQuery);
    $brandRow = mysqli_fetch_assoc($brandResult);
    $brand_name = mysqli_real_escape_string($conn, $brandRow['name']);

    
    $categoryQuery = "SELECT category_name FROM category WHERE id = $category_id";
    $categoryResult = mysqli_query($conn, $categoryQuery);
    $categoryRow = mysqli_fetch_assoc($categoryResult);
    $category_name = mysqli_real_escape_string($conn, $categoryRow['category_name']);

    
    $productQuery = "SELECT productname FROM products WHERE id = $product_id";
    $productResult = mysqli_query($conn, $productQuery);
    $productRow = mysqli_fetch_assoc($productResult);
    $productname = mysqli_real_escape_string($conn, $productRow['productname']);

    // Default image path if no file is uploaded
    $image_path = '';

    // Handling image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $image_name = time() . "_" . basename($image['name']); // Unique file name
        $upload_directory = 'uploads/';

        // Ensure the upload directory exists
        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }

        $image_path = $upload_directory . $image_name;

        // Move uploaded file
        if (!move_uploaded_file($image['tmp_name'], $image_path)) {
            die("Error uploading image.");
        }
    }

    // Insert into sales table
    $sql = "INSERT INTO sales (product_id, productname, category_id, category_name, brand_name, brand_id, sold_qty, amount, image) 
            VALUES ('$product_id','$productname', '$category_id', '$category_name', '$brand_name', '$brand_id', '$sold_qty', '$amount', '$image_path')";

    if (mysqli_query($conn, $sql)) {
        // Fetch the current stock quantity of the product
        $productQuery = "SELECT qty FROM products WHERE productname = '$productname' AND brand_id = $brand_id";
        $productResult = mysqli_query($conn, $productQuery);

        if (mysqli_num_rows($productResult) > 0) {
            $productRow = mysqli_fetch_assoc($productResult);
            $current_qty = (int) $productRow['qty'];

            // Calculate the new stock quantity
            $new_qty = $current_qty - $sold_qty;

            // Ensure the new quantity is not negative
            if ($new_qty < 0) {
                die("Error: Insufficient stock quantity.");
            }

            // Update the stock quantity in the products table
            $updateQuery = "UPDATE products SET qty = $new_qty WHERE productname = '$productname' AND brand_id = $brand_id";
            if (!mysqli_query($conn, $updateQuery)) {
                die("Error updating stock quantity: " . mysqli_error($conn));
            }
        } else {
            die("Error: Product not found.");
        }

        // Redirect after successful insertion
        header("Location: salesreport.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
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
<meta name="description" content="POS - Bootstrap Admin Template">
<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
<meta name="author" content="Dreamguys - Bootstrap Admin Template">
<meta name="robots" content="noindex, nofollow">
<title>Add Sales</title>

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
            <h4>Add Sales Report</h4>
            <h6>Create new sales report</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
        <form action="addsales.php" method="POST" enctype="multipart/form-data">
    <div class="row">
    <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Product Name:</label>
                <select name="product_id" required>
                    <option value="">Select Product</option>
                    <?php
                    // Fetch the available brands from the database
                    include('connection.php');
                    $productQuery = $conn->query("SELECT id, productname FROM products");
                    while ($product = $productQuery->fetch_assoc()) {
                        echo "<option value='{$product['id']}'>{$product['productname']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    // Fetch the available brands from the database
                    include('connection.php');
                    $categoryQuery = $conn->query("SELECT id, category_name FROM category");
                    while ($category = $categoryQuery->fetch_assoc()) {
                        echo "<option value='{$category['id']}'>{$category['category_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Sold Amount</label>
                <div class="input-group">
                    <span class="input-group-text">₱</span>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
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

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Sold Qty</label>
                <input type="number" name="quantity" required>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" id="imageUpload" accept="image/*" onchange="previewImage(event)">
            </div>
        </div>

        <div class="col-lg-12">
            <button type="submit" class="btn btn-submit">Submit</button>
            <a href="salesreport.php" class="btn btn-cancel">Cancel</a>
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