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

// เก็บค่าฟิลเตอร์ที่กดไว้ไว้ใน session หากมีการส่งค่า sort มา
if(isset($_GET['sort'])) {
    $_SESSION['sort'] = $_GET['sort'];
}

// ตรวจสอบ session และกำหนดค่าฟิลเตอร์จาก session หากมีค่า
if(isset($_SESSION['sort'])) {
    switch($_SESSION['sort']) {
        case 'sold_quantity_desc':
            $sort_column = 'Sold_Quantity';
            $sort_order = 'DESC';
            break;
        case 'sold_quantity_asc':
            $sort_column = 'Sold_Quantity';
            $sort_order = 'ASC';
            break;
        case 'view_count_desc':
            $sort_column = 'View_Count';
            $sort_order = 'DESC';
            break;
        case 'view_count_asc':
            $sort_column = 'View_Count';
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
        case 'book_remain_desc':
            $sort_column = 'Book_Remain';
            $sort_order = 'DESC';
            break;
        case 'book_remain_asc':
            $sort_column = 'Book_Remain';
            $sort_order = 'ASC';
            break;
        default:
            $sort_column = "ISBN";
            $sort_order = "ASC";
            break;
    }
} else {
    // ใช้ค่าเริ่มต้นหากไม่มีค่าใน session
    $sort_column = "ISBN";
    $sort_order = "ASC";
}

// Initialize $result to avoid undefined variable warning
$result = null;

// Initialize $start and $perPage variables
$start = 0;
$perPage = 7;

// Check if the query parameter exists
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

// Execute the query
$result = $conn->query($query);

?>



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
        $perPage = 7; // Number of records per page
        $start = ($page - 1) * $perPage; // Starting index for fetching records
        

        // กำหนดค่าเริ่มต้นของการเรียงข้อมูล
        $sort_column = "Sold_Quantity";
        $sort_order = "DESC";

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
                case 'view_count_desc':
                    $sort_column = 'View_Count';
                    $sort_order = 'DESC';
                    break;
                case 'view_count_asc':
                    $sort_column = 'View_Count';
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
                case 'book_remain_desc':
                        $sort_column = 'Book_Remain';
                        $sort_order = 'DESC';
                        break;
                case 'book_remain_asc':
                        $sort_column = 'Book_Remain';
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


        if (isset($_GET['edit'])) {
            $edit_isbn = $_GET['edit'];

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $edited_book_title = $_POST['edited_book_title'];
                $edited_price = $_POST['edited_price'];
                $edited_added_date = $_POST['edited_added_date'];
                $edited_quantity = $_POST['edited_quantity'];

                $update_query = "UPDATE book
                                SET Book_Name = ?, Book_Price = ?,
                                    Add_Date = ?, Book_Remain = ? ";


                $update_query .= " WHERE ISBN = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("ssssi", $edited_book_title, $edited_price, $edited_added_date, $edited_quantity, $edit_isbn);


                if ($stmt->execute()) {
                    echo "แก้ไขข้อมูลสำเร็จ!";
                } else {
                    echo "Error: " . $conn->error;
                }

                $stmt->close();
            }

            $select_query = "SELECT * FROM book WHERE ISBN = ?";
            $stmt = $conn->prepare($select_query);
            $stmt->bind_param("s", $edit_isbn);
            $stmt->execute();
            $edit_result = $stmt->get_result();

            if ($edit_result->num_rows > 0) {
                $edit_row = $edit_result->fetch_assoc();

                echo '<h2>แก้ไขข้อมูลหนังสือ</h2>';
                echo '<form action="?edit=' . $edit_isbn . '&page=' . $page . '" method="post" enctype="multipart/form-data">';
                echo '<label for="edited_book_title">ชื่อหนังสือ:</label>';
                echo '<input type="text" name="edited_book_title" value="' . $edit_row['Book_Name'] . '" required><br>';

                echo '<label for="edited_price">ราคา:</label>';
                echo '<input type="number" step="0.01" name="edited_price" value="' . $edit_row['Book_Price'] . '" required><br>';

                echo '<label for="edited_added_date">วันที่เพิ่ม:</label>';
                echo '<input type="date" name="edited_added_date" value="' . $edit_row['Add_Date'] . '" required><br>';

                echo '<label for="edited_quantity">จำนวนหนังสือ:</label>';
                echo '<input type="number" name="edited_quantity" value="' . $edit_row['Book_Remain'] . '" required><br>';

                // echo '<label for="edited_image">รูปภาพ:</label>';
                // echo '<input type="file" name="edited_image" class="form-control" accept="image/*"><br>';

                echo '<button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>';
                echo '&nbsp;';
                echo '<a href="admin.php"' . $page . '" class="btn btn-secondary">กลับหน้าแรก</a>';
                echo '</form>';
            } else {
                echo "ไม่พบข้อมูลหนังสือที่ต้องการแก้ไข";
            }

            $stmt->close();
        }

        if (isset($_POST['edited_book_title'])) {
            $edited_book_title = $_POST['edited_book_title'];
            $edited_price = $_POST['edited_price'];
            $edited_added_date = $_POST['edited_added_date'];
            $edited_quantity = $_POST['edited_quantity'];

            $update_query = "UPDATE book
                            SET Book_Name = ?, Book_Price = ?,
                                Add_Date = ?, Book_Remain = ? ";


            $update_query .= " WHERE ISBN = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssssi", $edited_book_title, $edited_price, $edited_added_date, $edited_quantity, $edit_isbn);

            if ($stmt->execute()) {
                echo "แก้ไขข้อมูลสำเร็จ!";
            } else {
                echo "Error: " . $conn->error;
            }

            $stmt->close();
        }

        if (isset($_GET['delete'])) {
            $delete_isbn = $_GET['delete'];

            $delete_query = "DELETE FROM Book WHERE ISBN = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param("s", $delete_isbn);

            if ($stmt->execute()) {
                echo '<script>alert("ลบข้อมูลสำเร็จ!");</script>';
                echo '<script>window.location.href = "admin.php";</script>';
                exit;
            } else {
                echo "Error: " . $conn->error;
            }

            $stmt->close();
        }

        // แสดงข้อมูลหนังสือในตาราง
        echo '<table class="table table-bordered">';
        echo '<tr>';
        echo '<th>ISBN</th>';
        echo '<th>ชื่อหนังสือ</th>';
        echo '<th>ราคา</th>';
        echo '<th>วันที่เพิ่ม</th>';
        echo '<th>จำนวนคงเหลือ <a href="?sort=book_remain_desc">&#9660;</a> <a href="?sort=book_remain_asc">&#9650;</a></th>';
        echo '<th>จำนวนครั้งที่อ่าน <a href="?sort=view_count_desc">&#9660;</a> <a href="?sort=view_count_asc">&#9650;</a></th>';
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
            echo '<td>' . $bookRow['View_Count'] . '</td>';
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
            // Build the pagination link with current filter parameters
            $paginationLink = "?page=$i"; // Starting with the page number
        
            // Append filter parameters to the pagination link
            if(isset($_GET['sort'])) {
                $paginationLink .= "&sort=" . urlencode($_GET['sort']);
            }
            // Continue appending other filter parameters...
        
            // Output the pagination link
            echo '<li class="page-item ' . ($page == $i ? "active" : "") . '"><a class="page-link" href="' . $paginationLink . '">' . $i . '</a></li>';
        }
        echo '</ul>';
        
        
        
        ?>

        <a href="adproduct.php" class="btn btn-success">เพิ่มข้อมูลหนังสือ</a>

        <?php $conn->close(); ?>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy6nA+2N7uJFmDp4RpuC7t7IXflAzHawZ" crossorigin="anonymous"></script>
    </body>

</html>
