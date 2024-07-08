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

// Process search query if submitted
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
    $age_min = isset($_GET['age_min']) ? $_GET['age_min'] : '';
    $age_max = isset($_GET['age_max']) ? $_GET['age_max'] : '';
    $book_type = isset($_GET['book_type']) ? $_GET['book_type'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $province = isset($_GET['province']) ? $_GET['province'] : '';
    $region = isset($_GET['region']) ? $_GET['region'] : '';
    $author = isset($_GET['author']) ? $_GET['author'] : '';
    $publisher = isset($_GET['publisher']) ? $_GET['publisher'] : '';
    $stat = isset($_GET['stat']) ? $_GET['stat'] : 'Approved';
        // Prepare SQL query to retrieve orders matching the search term, date range, gender, and age range
        $sql = "SELECT c.*, bb.*, g.*, b.*, p.*, a.*,od.*,bt.*,ct.*,au.*,pu.*
        FROM cart c 
        INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
        INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
        INNER JOIN book b ON od.ISBN = b.ISBN
        INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
        INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
        INNER JOIN author au ON b.Auth_ID = au.Auth_ID
        INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
        INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
        INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
        INNER JOIN area a ON p.Area_ID = a.Area_ID
        WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'";

// Append additional conditions based on gender and age range
if ($gender !== '') {
    $sql .= " AND bb.Gender_ID = '$gender'";
}
if ($age_min !== '' && $age_max !== '') {
    $sql .= " AND (YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) BETWEEN $age_min AND $age_max";
}
if ($book_type !== '') {
    $sql .= " AND b.Type_ID = '$book_type'";
}
if ($category !== '') {
    $sql .= " AND b.Cate_ID = '$category'";
}
if ($province !== '') {
    $sql .= " AND bb.Prov_ID = '$province'";
}
if ($region !== '') {
    $sql .= " AND p.Area_ID = '$region'";
}
if ($author !== '') {
    $sql .= " AND b.Auth_ID = '$author'";
}
if ($publisher !== '') {
    $sql .= " AND b.Publ_ID = '$publisher'";
}
if ($stat !== '') {
    $sql .= " AND c.Order_Status = '$stat'";
}

// Group by Cart_ID to aggregate results
$sql .= " GROUP BY c.Cart_ID";

    } else {
        // If search query is not submitted, retrieve all orders
        $sql = "SELECT c.*, bb.*, g.*, b.*, p.*, a.*,od.*,bt.*,ct.*,au.*,pu.*
        FROM cart c 
        INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
        INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
        INNER JOIN book b ON od.ISBN = b.ISBN
        INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
        INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
        INNER JOIN author au ON b.Auth_ID = au.Auth_ID
        INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
        INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
        INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
        INNER JOIN area a ON p.Area_ID = a.Area_ID
        GROUP BY c.Cart_ID";
    }

$result = $conn->query($sql);

// Pagination variables
$perPage = 7; // Number of records per page
$totalResults = $result->num_rows; // Total number of records
$totalPages = ceil($totalResults / $perPage); // Total number of pages
$page = isset($_GET['page']) && $_GET['page'] <= $totalPages ? (int)$_GET['page'] : 1; // Current page number
$start = ($page - 1) * $perPage; // Starting index for fetching records

// กำหนดค่าเริ่มต้นของการเรียงลำดับ
$order_by = isset($_GET['order']) ? $_GET['order'] : '';

// เพิ่มเงื่อนไขในคิวรี SQL สำหรับการเรียงลำดับตามจำนวนหนังสือและ Total Price
if ($order_by === 'book_quantity_asc') {
    $sql .= " ORDER BY od.Book_Quantity ASC";
} elseif ($order_by === 'book_quantity_desc') {
    $sql .= " ORDER BY od.Book_Quantity DESC";
}

// เพิ่มเงื่อนไขในคิวรี SQL สำหรับการเรียงลำดับตามวันที่และ Total Price
if ($order_by === 'buy_date_asc') {
    $sql .= " ORDER BY c.Buy_Date ASC";
} elseif ($order_by === 'buy_date_desc') {
    $sql .= " ORDER BY c.Buy_Date DESC";
} elseif ($order_by === 'total_price_asc') {
    $sql .= " ORDER BY c.Total_Price ASC";
} elseif ($order_by === 'total_price_desc') {
    $sql .= " ORDER BY c.Total_Price DESC";
}







// Retrieve records for the current page
$sql .= " LIMIT $start, $perPage";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
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
                <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link">ยอดขาย</a></li>
                <li class="nav-item"><a href="#" class="nav-link">สถิติผู้ซื้อ</a></li>
            </ul>

            <?php if (isset($_SESSION["User_ID"]) && isset($_SESSION["User_Name"]) && isset($_SESSION["User_Email"])) { ?>
                <span class="navbar-text me-3">
                    Logged in : <?= $_SESSION["User_Email"] ?>
                </span>
                <form action="" method="post">
                    <button type="submit" name="logout" class="btn btn-outline-danger">Logout</button>
                </form>
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
        <h2 class="text-center">Dashboard Content</h2>

        <!-- Search form -->
        <form class="mb-3" action="" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placholder="ค้นหาข้อมูลการสั่งซื้อ">
                <input type="date" name="start_date" class="form-control" 
                placeholder="วันที่เริ่มต้น (YYYY-MM-DD)" 
                value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : '2022-01-01'; ?>">
                <input type="date" name="end_date" class="form-control" 
                placeholder="วันที่สิ้นสุด (YYYY-MM-DD)" 
                value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); ?>">

                <select name="gender" class="form-select">
                    <option value="">เพศ</option>
                    <?php
                    $genderSql = "SELECT * FROM gender";
                    $genderResult = $conn->query($genderSql);
                    if ($genderResult->num_rows > 0) {
                        while ($genderRow = $genderResult->fetch_assoc()) {
                            echo "<option value='" . $genderRow["Gender_ID"] . "'>" . $genderRow["Gender_Name"] . "</option>";
                        }
                    }
                    ?>
                </select>
                <select name="book_type" class="form-select">
                    <option value="">ประเภทหนังสือ</option>
                    <?php
                    $bookTypeSql = "SELECT * FROM book_type";
                    $bookTypeResult = $conn->query($bookTypeSql);
                    if ($bookTypeResult->num_rows > 0) {
                        while ($bookTypeRow = $bookTypeResult->fetch_assoc()) {
                            echo "<option value='" . $bookTypeRow["Type_ID"] . "'>" . $bookTypeRow["Type_Name"] . "</option>";
                        }
                    }
                    ?>
                </select>
                <select name="category" class="form-select">
                    <option value="">หมวดหมู่</option>
                    <?php
                    $categorySql = "SELECT * FROM category";
                    $categoryResult = $conn->query($categorySql);
                    if ($categoryResult->num_rows > 0) {
                        while ($categoryRow = $categoryResult->fetch_assoc()) {
                            echo "<option value='" . $categoryRow["Cate_ID"] . "'>" . $categoryRow["Cate_Name"] . "</option>";
                        }
                    }
                    ?>
                </select>
                <select name="province" class="form-select">
                    <option value="">จังหวัด</option>
                    <?php
                    $provinceSql = "SELECT * FROM province";
                    $provinceResult = $conn->query($provinceSql);
                    if ($provinceResult->num_rows > 0) {
                        while ($provinceRow = $provinceResult->fetch_assoc()) {
                            echo "<option value='" . $provinceRow["Prov_ID"] . "'>" . $provinceRow["Prov_Name"] . "</option>";
                        }
                    }
                    ?>
                </select>
                <select name="region" class="form-select">
                    <option value="">ภูมิภาค</option>
                    <?php
                    $regionSql = "SELECT * FROM area";
                    $regionResult = $conn->query($regionSql);
                    if ($regionResult->num_rows > 0) {
                        while ($regionRow = $regionResult->fetch_assoc()) {
                            echo "<option value='" . $regionRow["Area_ID"] . "'>" . $regionRow["Area_Name"] . "</option>";
                        }
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">ค้นหา</button>
                
                <div class="input-group">
                    <span class="input-group-text">อายุ</span>
                    <input type="number" name="age_min" class="form-control" placeholder="อายุต่ำสุด">
                    <span class="input-group-text">ถึง</span>
                    <input type="number" name="age_max" class="form-control" placeholder="อายุสูงสุด">
                </div>
                <select name="author" class="form-select">
                    <option value="">ผู้แต่ง</option>
                    <?php
                    $regionSql = "SELECT * FROM author";
                    $regionResult = $conn->query($regionSql);
                    if ($regionResult->num_rows > 0) {
                        while ($regionRow = $regionResult->fetch_assoc()) {
                            echo "<option value='" . $regionRow["Auth_ID"] . "'>" . $regionRow["Auth_Name"] . "</option>";
                        }
                    }
                    ?>
                </select>
                <select name="publisher" class="form-select">
                    <option value="">สำนักพิมพ์</option>
                    <?php
                    $regionSql = "SELECT * FROM publisher";
                    $regionResult = $conn->query($regionSql);
                    if ($regionResult->num_rows > 0) {
                        while ($regionRow = $regionResult->fetch_assoc()) {
                            echo "<option value='" . $regionRow["Publ_ID"] . "'>" . $regionRow["Publ_Name"] . "</option>";
                        }
                    }
                    ?>
                </select>
                <select name="stat" class="form-select">
                    <option value="">สถานะคำสั่งซื้อ</option>
                    <option value="รอตรวจสอบ">รอตรวจสอบ</option>
                    <option value="Approved">อนุมัติ</option>
                    <option value="Disapproved">ไม่อนุมัติ</option>
                </select>
            </div>
        </form>

        <!-- Display orders -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cart ID</th>
                    <th>ผู้ซื้อ</th>
                    <th>Gender</th>
                    <th>หนังสือ</th>
                    <th><div class="d-flex align-items-center">
                        <span>จำนวน</span>
                        <a href="?order=book_quantity_asc" class="btn btn-sm btn-outline-primary mx-1">▲</a>
                        <a href="?order=book_quantity_desc" class="btn btn-sm btn-outline-primary">▼</a></div></th>
                    <th>ประเภทหนังสือ</th>
                    <th>หมวดหมู่หนังสือ</th>
                    <th>ผู้แต่ง</th>
                    <th>สำนักพิมพ์</th>
                    <th><div class="d-flex align-items-center">
                        <span>Total Price</span>
                        <a href="?order=total_price_asc" class="btn btn-sm btn-outline-primary mx-1">▲</a>
                        <a href="?order=total_price_desc" class="btn btn-sm btn-outline-primary">▼</a></div></th>
                    <th><div class="d-flex align-items-center">
                        <span>วันและเวลาที่ซื้อ</span>
                        <a href="?order=buy_date_asc" class="btn btn-sm btn-outline-primary mx-1">▲</a>
                        <a href="?order=buy_date_desc" class="btn btn-sm btn-outline-primary">▼</a></div></th><!-- Changed to display Buy Date -->
                    <th>สถานะคำสั่งซื้อ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["Cart_ID"] . "</td>";
                        echo "<td>" . $row["BBuy_Name"] . "</td>";
                        echo "<td>" . $row["Gender_Name"] . "</td>";
                        echo "<td>" . $row["Book_Name"] . "</td>";
                        echo "<td>" . $row["Book_Quantity"] . "</td>";
                        echo "<td>" . $row["Type_Name"] . "</td>";
                        echo "<td>" . $row["Cate_Name"] . "</td>";
                        echo "<td>" . $row["Auth_Name"] . "</td>";
                        echo "<td>" . $row["Publ_Name"] . "</td>";
                        echo "<td>" . $row["Total_Price"] . "</td>";
                        echo "<td>" . $row["Buy_Date"] . "</td>";
                        echo "<td>" . $row["Order_Status"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>ไม่พบข้อมูล</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <ul class="pagination justify-content-center">
    <?php
    for ($i = 1; $i <= $totalPages; $i++) {
        // Build the pagination link with current filter parameters
        $paginationLink = "?page=$i"; // Starting with the page number
        if (isset($_GET['search'])) {
            $paginationLink .= "&search=" . urlencode($_GET['search']);
        }
        if (isset($_GET['start_date'])) {
            $paginationLink .= "&start_date=" . urlencode($_GET['start_date']);
        }
        if (isset($_GET['end_date'])) {
            $paginationLink .= "&end_date=" . urlencode($_GET['end_date']);
        }
        if (isset($_GET['gender'])) {
            $paginationLink .= "&gender=" . urlencode($_GET['gender']);
        }
        if (isset($_GET['age_min'])) {
            $paginationLink .= "&age_min=" . urlencode($_GET['age_min']);
        }
        if (isset($_GET['age_max'])) {
            $paginationLink .= "&age_max=" . urlencode($_GET['age_max']);
        }
        if (isset($_GET['book_type'])) {
            $paginationLink .= "&book_type=" . urlencode($_GET['book_type']);
        }
        if (isset($_GET['category'])) {
            $paginationLink .= "&category=" . urlencode($_GET['category']);
        }
        if (isset($_GET['province'])) {
            $paginationLink .= "&province=" . urlencode($_GET['province']);
        }
        if (isset($_GET['region'])) {
            $paginationLink .= "&region=" . urlencode($_GET['region']);
        }
        if (isset($_GET['order'])) {
            $paginationLink .= "&order=" . urlencode($_GET['order']);
        }
        if (isset($_GET['author'])) {
            $paginationLink .= "&authorr=" . urlencode($_GET['author']);
        }
        if (isset($_GET['publisher'])) {
            $paginationLink .= "&publisher=" . urlencode($_GET['publisher']);
        }
        if (isset($_GET['stat'])) {
            $paginationLink .= "&stat=" . urlencode($_GET['stat']);
        }

        // Output the pagination link
        echo '<li class="page-item ' . ($page == $i ? "active" : "") . '"><a class="page-link" href="' . $paginationLink . '">' . $i . '</a></li>';
    }
    ?>
</ul>


        <!-- Moved the button here -->
        <div class="text-center">
            <a href="order_admin.php" class="btn btn-secondary">กลับหน้ารายการสั่งซื้อ</a>
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy6nA+2N7uJFmDp4RpuC7t7IXflAzHawZ" crossorigin="anonymous"></script>
</body>

</html>
