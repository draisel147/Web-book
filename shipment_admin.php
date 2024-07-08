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
        <h2 class="text-center">รายการจัดส่ง</h2>
        <form class="mb-3" action="" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="ค้นหารายกาจัดส่ง">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
            </div>
        </form>
        <?php
        // Include config file
        include 'config.php';

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;

        $start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

        // รับค่าคำค้นหาจากฟอร์ม
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // หาจำนวนรายการทั้งหมด
        $totalItems = $conn->query("SELECT COUNT(*) as total FROM cart 
            INNER JOIN order_detail ON cart.Cart_ID = order_detail.Cart_ID
            INNER JOIN book ON order_detail.ISBN = book.ISBN
            WHERE 
            book.Type_ID = '1' AND
            cart.Order_Status = 'Approved' AND
            (cart.Cart_ID LIKE '%$search%' OR
            cart.BBuy_ID LIKE '%$search%' OR
            cart.Total_Price LIKE '%$search%' OR
            cart.Buy_Date LIKE '%$search%' OR
            cart.Order_Score LIKE '%$search%' OR
            cart.Order_Review LIKE '%$search%' OR
            cart.Order_Status LIKE '%$search%')")->fetch_assoc()['total'];

        // คำนวณจำนวนหน้า
        $totalPages = ceil($totalItems / $perPage);

        // หา offset ตามหน้าปัจจุบัน
        $start = ($page - 1) * $perPage;

        // สร้างคำสั่ง SQL สำหรับดึงข้อมูลหน้านั้น
        $sql = "SELECT * FROM cart 
            INNER JOIN order_detail ON cart.Cart_ID = order_detail.Cart_ID
            INNER JOIN book ON order_detail.ISBN = book.ISBN
            WHERE 
            book.Type_ID = '1' AND
            cart.Order_Status = 'Approved' AND
            (cart.Cart_ID LIKE '%$search%' OR
            cart.BBuy_ID LIKE '%$search%' OR
            cart.Total_Price LIKE '%$search%' OR
            cart.Buy_Date LIKE '%$search%' OR
            cart.Order_Score LIKE '%$search%' OR
            cart.Order_Review LIKE '%$search%' OR
            cart.Order_Status LIKE '%$search%')
            ORDER BY cart.Buy_Date DESC
            LIMIT $start, $perPage";

        // ดึงข้อมูลจากฐานข้อมูล
        $result = $conn->query($sql);

        // แสดงข้อมูลในหน้า order_admin
        if ($result->num_rows > 0) {
            echo "<table class='table table-striped table-bordered'>
                <thead>
                    <tr>
                        <th>Cart ID</th>
                        <th>BBuy ID</th>
                        <th>Buy Date</th>
                        <th>Shipment Status</th>
                        <th>Address</th>
                        <th>EDIT</th>
                    </tr>
                </thead>
                <tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["Cart_ID"] . "</td>
                        <td>" . $row["BBuy_ID"] . "</td>
                        <td>" . $row["Buy_Date"] . "</td>
                        <td>" . $row["Shipment_Status"] . "</td>
                        <td>" . $row["Ship_Address"] . "</td>
                        <td><a href='edit_shipment.php?cart_id=" . $row["Cart_ID"] . "'>Edit</a></td>
                    </tr>";
            }

            echo "</tbody>
                </table>";
        } else {
            echo "<p class='text-center'>ไม่พบข้อมูล</p>";
        }

        // สร้างลิงก์ Pagination
        echo '<nav aria-label="Page navigation example">';
        echo '<ul class="pagination justify-content-center">';
        $disabledPrevious = ($page == 1) ? "disabled" : "";
        echo '<li class="page-item ' . $disabledPrevious . '"><a class="page-link" href="?page=' . ($page - 1) . '&search=' . $search . '">Previous</a></li>';
        for ($i = max(1, $page - 2); $i <= min($page + 2, $totalPages); $i++) {
            $active = ($page == $i) ? "active" : "";
            echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&search=' . $search . '">' . $i . '</a></li>';
        }
        $disabledNext = ($page == $totalPages) ? "disabled" : "";
        echo '<li class="page-item ' . $disabledNext . '"><a class="page-link" href="?page=' . ($page + 1) . '&search=' . $search . '">Next</a></li>';
        echo '</ul>';
        echo '</nav>';

        // ปิดการเชื่อมต่อ
        $conn->close();
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy6nA+2N7uJFmDp4RpuC7t7IXflAzHawZ"
            crossorigin="anonymous"></script>
</body>

</html>
