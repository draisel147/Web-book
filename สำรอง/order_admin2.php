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

// Display the rest of your HTML code
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แสดงข้อมูลคำสั่งซื้อ</title>
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
            <li class="nav-item"><a href="#" class="nav-link">Dashboard</a></li>
            <li class="nav-item"><a href="#" class="nav-link">ยอดขาย</a></li>
            <li class="nav-item"><a href="#" class="nav-link">สถิติผู้ซื้อ</a></li>
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
    <form class="mb-3" action="" method="GET">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาหนังสือ">
            <button type="submit" class="btn btn-primary">ค้นหา</button>
        </div>
    </form>
    <?php
    // Include config file
    include 'config.php';

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 5;

    $start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

    // รับค่าคำค้นหาจากฟอร์ม
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // 2. สร้างคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง cart
    $sql = "SELECT * FROM cart WHERE 
        Cart_ID LIKE '%$search%' OR
        BBuy_ID LIKE '%$search%' OR
        Total_Price LIKE '%$search%' OR
        Buy_Date LIKE '%$search%' OR
        Order_Score LIKE '%$search%' OR
        Order_Review LIKE '%$search%' OR
        Order_Status LIKE '%$search%'
        LIMIT $start, $perPage";

    // 3. ดึงข้อมูลจากฐานข้อมูล
    $result = $conn->query($sql);

    // 4. แสดงข้อมูลในหน้า order_admin
    if ($result->num_rows > 0) {
        echo "<table class='table table-striped table-bordered'>
            <thead>
                <tr>
                    <th>Cart ID</th>
                    <th>BBuy ID</th>
                    <th>Total Price</th>
                    <th>Buy Date</th>
                    <th>Order Score</th>
                    <th>Order Review</th>
                    <th>Order Status</th>
                    <th>Confirmation</th>
                    <th>EDIT</th>
                </tr>
            </thead>
            <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["Cart_ID"] . "</td>
                    <td>" . $row["BBuy_ID"] . "</td>
                    <td>" . $row["Total_Price"] . "</td>
                    <td>" . $row["Buy_Date"] . "</td>
                    <td>" . $row["Order_Score"] . "</td>
                    <td>" . $row["Order_Review"] . "</td>
                    <td>" . $row["Order_Status"] . "</td>
                    <td>" . $row["แก้ไข"] . "</td>
                    <td>";

            // ตรวจสอบว่ามี Confirmation หรือไม่
            if (isset($row["Confirmation"])) {
                // กำหนด path ของไฟล์ภาพ
                $imagePath = $row["Confirmation"];

                // ตรวจสอบว่าไฟล์ภาพมีอยู่หรือไม่
                if (file_exists($imagePath)) {
                    // แสดงรูปภาพ
                    echo "<a href='$imagePath' target='_blank'><img src='$imagePath' alt='Confirmation' height='50'></a>";
                } else {
                    // แสดงข้อความถ้าไม่พบไฟล์ภาพ
                    echo "ไม่พบไฟล์ภาพ";
                }
            }

            echo "</td>
                </tr>";
        }

        echo "</tbody>
            </table>";
    } else {
        echo "<p class='text-center'>ไม่พบข้อมูล</p>";
    }

    // สร้างลิงก์ Pagination
    $totalPages = ceil($conn->query("SELECT * FROM cart")->num_rows / $perPage);

    echo '<ul class="pagination justify-content-center">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&search=' . $search . '">' . $i . '</a></li>';
    }
    echo '</ul>';

    // ปิดการเชื่อมต่อ
    $conn->close();
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy6nA+2N7uJFmDp4RpuC7t7IXflAzHawZ"
        crossorigin="anonymous"></script>
</body>

</html>
