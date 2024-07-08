<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แสดงข้อมูลหนังสือ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
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
                <li class="nav-item"><a href="http://localhost/web/dashboard.php?search=&start_date=2022-01-01&end_date=2024-02-17&gender=&book_type=&category=&province=&region=&age_min=&age_max=&author=&publisher=&stat=" class="nav-link">Dashboard</a></li>
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
        <h2 class="text-center">รายการหนังสือ</h2>
        <form class="mb-3" action="" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาหนังสือ">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
            </div>
        </form>

        <?php
        include 'config.php';

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 4;

        $start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

        // กำหนดค่าเริ่มต้นของการเรียงข้อมูล
        $sort_column = "ISBN";
        $sort_order = "ASC";

        // เช็คว่ามีการส่งพารามิเตอร์ sort มาหรือไม่
        if(isset($_GET['sort'])) {
            switch($_GET['sort']) {
                case 'sold_quantity_desc':
                    $sort_column = 'Sold_Quantity';
                    $sort_order = 'DESC';
                    break;
                case 'sold_quantity_asc':
                    $sort_column = 'Sold_Quantity';
                    $sort_order = 'ASC';
                    break;
                case 'read_count_desc':
                    $sort_column = 'Read_Count';
                    $sort_order = 'DESC';
                    break;
                case 'read_count_asc':
                    $sort_column = 'Read_Count';
                    $sort_order = 'ASC';
                    break;
                case 'days_in_stock_desc':
                    $sort_column = 'Days_In_Stock';
                    $sort_order = 'DESC';
                    break;
                case 'days_in_stock_asc':
                    $sort_column = 'Days_In_Stock';
                    $sort_order = 'ASC';
                    break;
            }
        }

        // ทำคำสั่ง SQL เพื่อดึงข้อมูลจากฐานข้อมูล โดยใช้เงื่อนไขการเรียงข้อมูล
        if(isset($_GET['search'])) {
            $search = $_GET['search'];
            $query = "SELECT 
                        b.ISBN, 
                        b.Book_Name, 
                        b.Book_Price, 
                        b.Add_Date, 
                        b.Book_Remain, 
                        b.View_Count, 
                        b.Book_Image,
                        SUM(CASE WHEN c.Order_Status = 'Approved' THEN 1 ELSE 0 END) AS Sold_Quantity,
                        DATEDIFF(NOW(), b.Add_Date) AS Days_In_Stock, 
                        o.Read_Count
                    FROM 
                        Book b
                    LEFT JOIN 
                        order_detail od ON b.ISBN = od.ISBN
                    LEFT JOIN 
                        own o ON b.ISBN = o.ISBN
                    LEFT JOIN 
                        cart c ON od.Cart_ID = c.Cart_ID                    
                      WHERE 
                        b.Book_Name LIKE '%$search%'
                      GROUP BY 
                        b.ISBN
                      ORDER BY 
                        $sort_column $sort_order
                      LIMIT $start, $perPage";
        } else {
            $query = "SELECT 
                        b.ISBN, 
                        b.Book_Name, 
                        b.Book_Price, 
                        b.Add_Date, 
                        b.Book_Remain, 
                        b.View_Count, 
                        b.Book_Image,
                        SUM(CASE WHEN c.Order_Status = 'Approved' THEN 1 ELSE 0 END) AS Sold_Quantity, 
                        DATEDIFF(NOW(), b.Add_Date) AS Days_In_Stock,
                        o.Read_Count
                    FROM 
                        Book b
                    LEFT JOIN 
                        order_detail od ON b.ISBN = od.ISBN
                    LEFT JOIN 
                        own o ON b.ISBN = o.ISBN
                    LEFT JOIN 
                        cart c ON od.Cart_ID = c.Cart_ID
                      GROUP BY 
                        b.ISBN
                      ORDER BY 
                        $sort_column $sort_order
                      LIMIT $start, $perPage";
        }
        $result = $conn->query($query);

        // แสดงข้อมูลหนังสือในตาราง
        echo '<table class="table table-bordered">';
        echo '<tr>';
        echo '<th>ISBN <a href="?sort=sold_quantity_desc">&#9660;</a> <a href="?sort=sold_quantity_asc">&#9650;</a></th>';
        echo '<th>ชื่อหนังสือ</th>';
        echo '<th>ราคา</th>';
        echo '<th>วันที่เพิ่ม</th>';
        echo '<th>จำนวนคงเหลือ <a href="?sort=book_remain_desc">&#9660;</a> <a href="?sort=book_remain_asc">&#9650;</a></th>';
        echo '<th>จำนวนครั้งที่อ่าน <a href="?sort=read_count_desc">&#9660;</a> <a href="?sort=read_count_asc">&#9650;</a></th>';
        echo '<th>จำนวนที่ขาย <a href="?sort=sold_quantity_desc">&#9660;</a> <a href="?sort=sold_quantity_asc">&#9650;</a></th>';
        echo '<th>จำนวนวันที่อยู่ในสต็อก <a href="?sort=days_in_stock_desc">&#9660;</a> <a href="?sort=days_in_stock_asc">&#9650;</a></th>';
        echo '<th>รูปภาพ</th>';
        echo '<th>แก้ไข</th>';
        echo '<th>ลบ</th>';
        echo '</tr>';

        while ($bookRow = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $bookRow['ISBN'] . '</td>';
            echo '<td>' . $bookRow['Book_Name'] . '</td>';
            echo '<td>' . $bookRow['Book_Price'] . '</td>';
            echo '<td>' . $bookRow['Add_Date'] . '</td>';
            echo '<td>' . $bookRow['Book_Remain'] . '</td>';
            echo '<td>' . $bookRow['Read_Count'] . '</td>';
            echo '<td>' . $bookRow['Sold_Quantity'] . '</td>';
            echo '<td>' . $bookRow['Days_In_Stock'] . '</td>';
            echo '<td><img src="image/' . $bookRow['Book_Image'] . '" height="50" alt="รูปภาพ"></td>';
            echo '<td><a href="?edit=' . $bookRow['ISBN'] . '&page=' . $page . '" class="btn btn-warning">แก้ไข</a></td>';
            echo '<td><a href="?delete=' . $bookRow['ISBN'] . '&page=' . $page . '" onclick="return confirm(\'ลบหนังสือนี้?\')" class="btn btn-danger">ลบ</a></td>';
            echo '</tr>';
        }

        echo '</table>';

        $totalPages = ceil($conn->query("SELECT * FROM Book")->num_rows / $perPage);

        echo '<ul class="pagination justify-content-center">';
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }
        echo '</ul>';
        ?>

        <a href="adproduct.php" class="btn btn-success">เพิ่มข้อมูลหนังสือ</a>

        <?php $conn->close(); ?>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy6nA+2N7uJFmDp4RpuC7t7IXflAzHawZ" crossorigin="anonymous"></script>
    </body>

</html>
