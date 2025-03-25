<?php   
include('connection.php');

// Get the product ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid product ID.");
}
$product_id = (int)$_GET['id'];

// Fetch the product details
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Product not found.");
}
$product = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $productname = mysqli_real_escape_string($conn, $_POST['name']);  
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $flavor = mysqli_real_escape_string($conn, $_POST['flavor']);
    $brand_id = (int) $_POST['brand_id']; 
    $qty = (int) $_POST['quantity'];
    $price = (float) $_POST['price'];

    // Fetch the brand name
    $brandQuery = "SELECT name FROM brands WHERE id = $brand_id";
    $brandResult = mysqli_query($conn, $brandQuery);
    $brandRow = mysqli_fetch_assoc($brandResult);
    $brand_name = mysqli_real_escape_string($conn, $brandRow['name']);

    
    $categoryQuery = "SELECT category_name FROM category WHERE id = $category_id";
    $categoryResult = mysqli_query($conn, $categoryQuery);
    $categoryRow = mysqli_fetch_assoc($categoryResult);
    $category_name = mysqli_real_escape_string($conn, $categoryRow['category_name']);
    
    // Handle image upload
    $image_path = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $image_name = time() . "_" . basename($image['name']);
        $upload_directory = 'uploads/';
        
        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }

        $image_path = $upload_directory . $image_name;
        move_uploaded_file($image['tmp_name'], $image_path);
    }

    // Update product details
    $sql = "UPDATE products SET productname='$productname', category_name='$category_name', category_id='$category_id', flavor='$flavor', brand_name='$brand_name', brand_id='$brand_id', qty='$qty', price='$price', image='$image_path' WHERE id=$product_id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: productlist.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<title>Add Product</title>

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
            <h4>Product Update</h4>
            <h6>Update a product</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
        <form action="editproduct.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
            <label>Product Name:</label>
            <input type="text" name="name" value="<?php echo $product['productname']; ?>" required><br>
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
                <label>Flavor</label>
                <input type="text" name="flavor" value="<?php echo $product['flavor']; ?>" required><br>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
            <label>Brand:</label>
        <select name="brand_id" required>
            <option value="">Select Brand</option>
            <?php
            include('connection.php');
            $brandQuery = $conn->query("SELECT id, name FROM brands");
            while ($brand = $brandQuery->fetch_assoc()) {
                $selected = ($brand['id'] == $product['brand_id']) ? 'selected' : '';
                echo "<option value='{$brand['id']}' $selected>{$brand['name']}</option>";
            }
            ?>
        </select><br>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Qty</label>
                <input type="number" name="quantity" value="<?php echo $product['qty']; ?>" required><br>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Price</label>
                <input type="text" name="price" value="<?php echo $product['price']; ?>" required><br>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
    <div class="form-group">
    <label>Change Image:</label>
    <input type="file" name="image" accept="image/*"><br>
    </div>
</div>



        <div class="col-lg-12">
            <button type="submit" class="btn btn-submit">Update</button>
            <a href="productlist.php" class="btn btn-cancel">Cancel</a>
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