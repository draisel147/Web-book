<?php
session_start();

// Include config file
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["User_ID"])) {
    // If not logged in, redirect to the login page
    header("location: login.php");
    exit(); // Ensure that the script stops here to prevent further execution
}

// Logout logic
if (isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("location: login.php");
    exit();
}

// Check if Cart ID is provided in the URL
if (isset($_GET['cart_id'])) {
    $cartID = $_GET['cart_id'];

    // Fetch the order details from the database based on Cart ID
    $sql = "SELECT * FROM cart WHERE Cart_ID = '$cartID'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $cartData = $result->fetch_assoc();

        // Fetch order details related to the cart from Order Detail table
        $orderDetailsSql = "SELECT order_detail.*, book.ISBN, book.* 
                            FROM order_detail
                            INNER JOIN book ON order_detail.ISBN = book.ISBN
                            WHERE order_detail.Cart_ID = '$cartID'";
        
        $orderDetailsResult = $conn->query($orderDetailsSql);

    } else {
        // If no order found, you may want to handle this case
        echo "Order not found!";
        exit();
    }
} else {
    // If Cart ID is not provided in the URL, redirect to order_admin.php
    header("location: order_admin.php");
    exit();
}

// Display the rest of your HTML code
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แก้ไขข้อมูลคำสั่งซื้อ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
<div class="container-fluid">
        <header class="d-flex flex-wrap justify-content-between py-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
                <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
                <span class="fs-4">Admin</span>
            </a>

            <ul class="nav nav-pills">
                <li class="nav-item"><a href="admin.php" class="nav-link active" aria-current="page">Home</a></li>
                <li class="nav-item"><a href="order_admin.php" class="nav-link">Order</a></li>
                <li class="nav-item"><a href="shipment_admin.php" class="nav-link">Shipment</a></li>
                <li class="nav-item"><a href="http://localhost/web/dashboard.php?search=&start_date=2022-01-01&end_date=2024-02-17&gender=&book_type=&category=&province=&region=&age_min=&age_max=&author=&publisher=&stat=" class="nav-link">Dashboard</a></li>
            </ul>

            <?php if (isset($_SESSION["User_ID"]) && isset($_SESSION["User_Name"]) && isset($_SESSION["User_Email"])) { ?>
                <span class="navbar-text me-3">
                    Logged in : <?= $_SESSION["User_Email"] ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>&nbsp;&nbsp;
            <?php } ?>
        </header>
    </div>
    <div class="mt-4 ms-auto">
        <?php
        if (isset($_SESSION["User_ID"]) && isset($_SESSION["User_Name"]) && isset($_SESSION["User_Email"])) {
            echo "<h5 class='text-start'>ยินดีต้อนรับ " . $_SESSION["User_Name"] . "!</h5>";
        } else {
            echo "<h2 class='text-start'>ยินดีต้อนรับ ผู้เยี่ยมชม!</h2>";
            echo "<p class='text-start'>กรุณาเข้าสู่ระบบเพื่อดูเนื้อหา.</p>";
        }
        ?>
    </div>
    <div class="container">
        <h2 class="text-center">รายการคำสั่งซื้อ</h2>
        <div class="mb-3">
            <label for="cartId" class="form-label">Cart ID</label>
            <input type="text" class="form-control" id="cartId" value="<?= $cartData['Cart_ID'] ?>" readonly>
        </div>
        <table class='table table-striped table-bordered'>
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Book Title</th>
                    <th>Book Image</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($orderDetailRow = $orderDetailsResult->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $orderDetailRow["ISBN"] . "</td>
                            <td>" . $orderDetailRow["Book_Name"] . "</td>
                            <td><img src='image/" . $orderDetailRow["Book_Image"] . "' alt='Book Image' style='max-width: 100px; max-height: 100px;'></td>
                            <td>" . $orderDetailRow["Book_Quantity"] . "</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Buttons for Approve and Disapprove -->
        <div class="text-center mt-3">
            <a href='approve_order_logic.php?cart_id=<?= $cartData['Cart_ID'] ?>' class='btn btn-success'>Approve Order</a>
            <a href='disapprove_order_logic.php?cart_id=<?= $cartData['Cart_ID'] ?>' class='btn btn-danger'>Disapprove AOrder</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy6nA+2N7uJFmDp4RpuC7t7IXflAzHawZ" crossorigin="anonymous"></script>
</body>

</html>
