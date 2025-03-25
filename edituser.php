<?php
include('connection.php');

// Get the user ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid user ID.");
}
$id = (int)$_GET['id'];

// Fetch the user details
$query = "SELECT * FROM register_user WHERE id = $id";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("User not found.");
}
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $phone_no = trim($_POST['phone_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Phone number validation
$phone_no = '+63' . trim($_POST['phone_no']);
if (!preg_match('/^\+63\d{10}$/', $phone_no)) {
    die("<script>alert('Phone number must be 10 digits after +63 (e.g. 9123456781)'); window.history.back();</script>");
}
    
    // Initialize password update
    $password_update = '';
    $password_error = '';
    
    // Password validation
    if (!empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            if (strlen($_POST['new_password']) < 8) {
                die("<script>alert('Password must be at least 8 characters long'); window.history.back();</script>");
            }
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $password_update = ", password = '$new_password'";
        } else {
            die("<script>alert('Passwords do not match!'); window.history.back();</script>");
        }
    }
    
    // Update the user details
    $sql = "UPDATE register_user 
            SET firstname='$firstname', 
                lastname='$lastname', 
                phone_no='$phone_no', 
                email='$email', 
                status='$status'
                $password_update
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('User updated successfully!'); window.location.href='userlists.php';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<title>Edit User</title>
<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/animate.css">
<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<style>
.status-toggle {
    position: relative;
    display: inline-block;
    width: 75px;
    height: 34px;
}

.status-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.checktoggle {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
    padding-top: 7px;
    text-align: center;
    color: white;
    font-size: 12px;
}

.checktoggle:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .checktoggle {
    background-color: #28a745;
}

input:checked + .checktoggle:before {
    transform: translateX(40px);
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.btn {
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 1rem;
    line-height: 1.5;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.mt-4 {
    margin-top: 1.5rem !important;
}

.ml-2 {
    margin-left: 0.5rem !important;
}
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
    <!-- Header and Sidebar -->
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

    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Edit User</h4>
                    <h6>Update user details</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="edituser.php?id=<?php echo $id; ?>" method="POST">
                        <div class="row">
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
    <div class="form-group">
        <label>Phone Number</label>
        <div class="input-group">
            <span class="input-group-text" style="background-color: #e9ecef;">+63</span>
            <input type="tel" class="form-control" name="phone_no" 
                   id="phone_no" 
                   value="<?php echo substr(htmlspecialchars($user['phone_no'] ?? ''), 3); ?>" 
                   pattern="\d{10}" 
                   title="Must be 10 digits after +63 (e.g. 9123456781)" 
                   maxlength="10"
                   required>
        </div>
        <small class="text-muted">Format: 9123456781 (10 digits after +63)</small>
    </div>
</div>

                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <h5 class="mb-1">Change Password (leave blank to keep current password)</h5>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" placeholder="Enter new password">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password">
                                </div>
                            </div>
                            
                            <div class="col-lg-12 mt-4">
                                <button type="submit" class="btn btn-primary">Update User</button>
                                <a href="userlists.php" class="btn btn-secondary ml-2">Cancel</a>
                            </div>
                        </div>
                    </form>
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
<script>
function validatePhoneNumber() {
    const phoneInput = document.getElementById('phone_no');
    const phoneRegex = /^\d{10}$/;
    
    if (!phoneRegex.test(phoneInput.value)) {
        alert('Phone number must be 10 digits after +63 (e.g. 912345678)');
        phoneInput.focus();
        return false;
    }
    
    return true;
}
</script>

</body>
</html>
<?php mysqli_close($conn); ?>