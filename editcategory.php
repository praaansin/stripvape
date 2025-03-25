<?php   
include('connection.php');

// Get the category ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid category ID.");
}
$category_id = (int)$_GET['id'];

// Fetch the category details
$query = "SELECT * FROM category WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    die("Category not found.");
}
$category = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data safely
    $category_name = trim($_POST['name']);
    $brand_id = (int)$_POST['brand_id'];

    if (empty($category_name) || $brand_id == 0) {
        echo "Please fill all fields.";
    } else {
        // Prepare the update query
        $updateQuery = "UPDATE category SET category_name = ?, brand_id = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sii", $category_name, $brand_id, $category_id);

        // Execute and handle result
        if ($stmt->execute()) {
            header("Location: categorylist.php");
            exit();
        } else {
            echo "Error updating category: " . $conn->error;
        }
    }
}
$stmt->close();
$conn->close();
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
<title>Edit Category</title>

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
            <h4>Category Update</h4>
            <h6>Update a category</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="editcategory.php?id=<?php echo $category_id; ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
            <label>Category Name:</label>
            <input type="text" name="name" value="<?php echo $category['category_name']; ?>" required><br>
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
                $selected = ($brand['id'] == $category['brand_id']) ? 'selected' : '';
                echo "<option value='{$brand['id']}' $selected>{$brand['name']}</option>";
            }
            ?>
        </select><br>
            </div>
        </div>
        
        <div class="col-lg-12">
            <button type="submit" class="btn btn-submit">Update</button>
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