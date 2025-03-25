<?php
include 'connection.php'; // Include your database connection

$sql = "SELECT id, firstname, lastname, phone_no, email, status FROM register_user";
$result = mysqli_query($conn, $sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... other fields ...
    $status = (int)$_POST['status'];
    
    $sql = "UPDATE register_user 
            SET firstname='$firstname', 
                lastname='$lastname', 
                phone_no='$phone_no', 
                email='$email', 
                status='$status'
                $password_update
            WHERE id=$id";
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Delete query to remove the user from the database
    $delete_sql = "DELETE FROM register_user WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    
    // Check if deletion was successful
    if ($stmt->affected_rows > 0) {
        // Redirect back to the user list page with a success message
        echo "<script>window.location.href='userlists.php';</script>";
    } else {
        // In case of failure
        echo "<script>alert('Error deleting user.'); window.location.href='userlists.php';</script>";
    }
    
    // Close statement
    $stmt->close();
}
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
<title>Users</title>

<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg">

<link rel="stylesheet" href="assets/css/bootstrap.min.css">

<link rel="stylesheet" href="assets/css/animate.css">

<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

<link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">

<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

<link rel="stylesheet" href="assets/css/style.css">
<style>
   .search-input {
    position: relative;
    width: 200px;
}

.search-input input {
    padding: 8px 15px 8px 35px; /* Adjusted padding-left to make space for icon */
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.search-input .btn-searchset {
    position: absolute;
    left: 10px;
    top: 20%;
    transform: translateY(5%);
    background: transparent;
    border: none;
    padding: 0;
    cursor: pointer;
    z-index: 2;
}

.search-input .btn-searchset img {
    width: 18px;
    height: 18px;
    opacity: 0.7;
}

.search-input input::placeholder {
    color: #999;
    padding-left: -3px;
    letter-spacing: 0.5px;
}
</style>
</head>
<body>
<div class="main-wrapper">

<div class="header">

<div class="header-left active">
<a href="index.php" class="logo">
<img src="assets/img/logo2.png" alt="">
</a>
<a href="index.php" class="logo-small">
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
<h4>User list</h4>
<h6>View users</h6>
</div>
</div>
<div class="card">
<div class="card-body">
<div class="table-top">
<div class="search-set">
    <div class="search-input">
        <input type="text" id="searchInput" placeholder="Search...">
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
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Determine status text and styling
                        $status = isset($row['status']) ? $row['status'] : 0;
                        $statusText = ($status == 1) ? 'Active' : 'Inactive';
                        $statusColor = ($status == 1) ? 'background-color: #28a745; color: #fff;' : 'background-color: #dc3545; color: #fff;';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['firstname']) ?></td>
                            <td><?= htmlspecialchars($row['lastname']) ?></td>
                            <td><?= htmlspecialchars($row['phone_no']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <span class="badge" style="<?= $statusColor ?> padding: 5px 10px; border-radius: 5px;">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="edituser.php?id=<?= $row['id'] ?>" style="color: #4CAF50; text-decoration: none; font-weight: bold; padding: 5px;">Edit</a> |  
                                <a href="userlists.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');" style="color: #f44336; text-decoration: none; font-weight: bold; padding: 5px;">Delete</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">No users found.</td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- [Rest of the HTML remains the same, but we can remove the toggle switch JavaScript] -->

<script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/js/jquery.slimscroll.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/dataTables.bootstrap4.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
<script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
<script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
// Search function
$(document).ready(function(){
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});

// Print function
function printTable() {
    var printContents = document.querySelector('.table-responsive').outerHTML;
    var originalContents = document.body.innerHTML;
    
    document.body.innerHTML = `
        <html>
            <head>
                <title>User List</title>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                    th { background-color: #f2f2f2; }
                    .badge { padding: 5px 10px; border-radius: 5px; }
                </style>
            </head>
            <body>
                <h2>User List</h2>
                ${printContents}
            </body>
        </html>
    `;
    
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
</script>
</body>
</html>