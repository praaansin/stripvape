<?php  
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brandname = mysqli_real_escape_string($conn, $_POST['name']);  
   // Default image path if no file is uploaded
   $image_path = '';

   // Handling image upload
   if (isset($_FILES['brand_image']) && $_FILES['brand_image']['error'] == 0) {
       $image = $_FILES['brand_image'];
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
    // Insert the new brand with the image path
    $query = "INSERT INTO brands (name, brand_image) VALUES ('$brandname', '$image_path')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Brand added successfully!'); window.location.href='brandlist.php';</script>";
    } else {
        echo "<script>alert('Error adding brand: " . mysqli_error($conn) . "');</script>";
    }
}

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
    <title>Add Product</title>

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
                <h4>Product Add</h4>
                <h6>Create new product</h6>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
            <form action="addbrand.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="form-group">
                    <label>Brand Name</label>
                    <input type="text" name="name" required>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="brand_image" id="imageUpload" accept="image/*" onchange="previewImage(event)">
        </div>
    </div>



            <div class="col-lg-12">
                <button type="submit" class="btn btn-submit">Submit</button>
                <a href="brandlist.php" class="btn btn-cancel">Cancel</a>
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