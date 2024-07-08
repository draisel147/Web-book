<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["User_ID"])) {
    // If not logged in, redirect to the login page
    header("location: login.php");
    exit(); // Ensure that the script stops here to prevent further execution
}

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch BookBuyer points
$userID = $_SESSION["User_ID"];
$pointsQuery = "SELECT BBuy_Point FROM book_buyer WHERE BBuy_ID = '$userID'";
$pointsResult = $conn->query($pointsQuery);

// Check if the query was successful and fetch the points
if ($pointsResult->num_rows > 0) {
    $pointsRow = $pointsResult->fetch_assoc();
    $userPoints = $pointsRow["BBuy_Point"];
} else {
    $userPoints = 0; // Default to 0 if no points found
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <header data-bs-theme="dark">
        <div class="container mt-3">
            <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
                <a class="navbar-brand" href="homepage.php">Book Store</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="homepage.php">หน้าหลัก</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="orderstatus.php">สถานะการสั่งซื้อ</a></a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="mybook.php">หนังสือของฉัน</a>
                        </li>
                        <li class="nav-item active">
                            <a href="cart.php" class="btn btn-primary">ดูตะกร้าสินค้า</a>
                        </li>
                    </ul>
                    <form class="d-flex" role="search" method="get" action="search.php">
                        <input class="form-control me-2" type="search" placeholder="Search" name="q" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>&nbsp;
                    </form>
                    &nbsp;&nbsp;<?php if (isset($_SESSION["User_ID"]) && isset($_SESSION["User_Name"]) && isset($_SESSION["User_Email"])) { ?>
                        <span class="navbar-text me-3">
                            Logged in : <?= $_SESSION["User_Name"] ?>
                            Points : <?= $userPoints ?></p>
                        </span>
                        &nbsp;<a href="logout.php" class="btn btn-outline-danger">Logout</a>&nbsp;&nbsp;
                    <?php } ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Display user info and logout button -->
    <div class="mb-3">
        <p><strong>User ID:</strong> <?= $_SESSION["User_ID"] ?></p>
    </div>
    <?php
    // Display a welcome message based on whether the user is logged in or not
    if (isset($_SESSION["User_ID"]) && isset($_SESSION["User_Name"]) && isset($_SESSION["User_Email"])) {
        echo "<h4 class='text-start mt-5'>&nbsp;&nbsp;&nbsp;ยินดีต้อนรับ " . $_SESSION["User_Name"] . "!</h4>";
    } else {
        echo "<h2 class='text-start mt-5'>&nbsp;&nbsp;&nbsp;ยินดีต้อนรับ ผู้เยี่ยมชม!</h2>";
        echo "<p class='text-start'>กรุณาเข้าสู่ระบบเพื่อดูเนื้อหา.</p>";
    }
    ?>

    <div class="container mt-5">
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php
            // สร้างคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง own และ cart โดยใช้ ISBN เป็นคีย์เชื่อม
            $sql = "SELECT own.ISBN AS Own_ISBN, book.Type_ID, cart.Ship_Address, cart.Shipment_Status, cart.Tracking_ID, book.Book_Image, book.Book_Name
                FROM own
                LEFT JOIN (
                    SELECT c.BBuy_ID, od.ISBN AS Cart_ISBN, c.Ship_Address, c.Shipment_Status, c.Tracking_ID, book.*
                    FROM order_detail od
                    LEFT JOIN cart c ON od.Cart_ID = c.Cart_ID
                    LEFT JOIN book ON od.ISBN = book.ISBN
                ) AS cart ON own.BBuy_ID = cart.BBuy_ID AND own.ISBN = cart.Cart_ISBN
                LEFT JOIN book ON own.ISBN = book.ISBN
                WHERE own.BBuy_ID = " . $_SESSION["User_ID"];

            $result = $conn->query($sql);

            // ตรวจสอบว่ามีข้อมูลในตารางหรือไม่
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col">';
                    echo '<div class="card h-100">';
                    echo '<img src="image/' . $row['Book_Image'] . '" class="card-img-top" alt="' . $row['Book_Name'] . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $row['Book_Name'] . '</h5>';
                    echo '<p><strong>Type:</strong> ' . ($row['Type_ID'] == 2 ? "e-book" : "ปกอ่อน") . '</p>';
                    if ($row['Type_ID'] == 1) {
                        echo '<p><strong>Address:</strong> ' . $row['Ship_Address'] . '</p>';
                        echo '<p><strong>Shipment Status:</strong> ' . $row['Shipment_Status'] . '</p>';
                        echo '<p><strong>Track ID:</strong> ' . $row['Tracking_ID'] . '</p>';
                    }
                    if ($row['Type_ID'] == 2) { 
                        echo '<div class="card-body">';
                        echo '<a href="read.php?id=' . $row['Own_ISBN'] . '&BBuy_ID=' . $_SESSION["User_ID"] . '" class="btn btn-primary">อ่านหนังสือ</a>'; // Provide a link to read the book

                        echo '</div>';
                    }
                    
                    echo '<form method="post" action="submit_review.php" class="mt-2">';
                    echo '<div class="row">';
                    echo '<div class="col">';
                    echo '<input type="hidden" name="isbn" value="' . $row['Own_ISBN'] . '">';
                    echo '<label for="score" class="form-label">คะแนน (1-5):</label>';
                    echo '<input type="number" class="form-control" id="score" name="score" min="1" max="5" required>';
                    echo '</div>';
                    echo '<div class="col">';
                    echo '<label for="review" class="form-label">รีวิว:</label>';
                    echo '<textarea class="form-control" id="review" name="review" rows="2" required></textarea>';
                    echo '</div>';
                    echo '</div>';
                    echo '<button type="submit" class="btn btn-primary mt-2">ส่งรีวิว</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                }
            } else {
                echo "<p class='text-start container mt-5'>คุณยังไม่มีหนังสือในรายการ</p>";
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-eRqNqblF8Vg3mZ8N3SvRMJoPTeBDJjlvL/Jzx6n5CzoI" crossorigin="anonymous"></script>

    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <div class="col-md-4 d-flex align-items-center">
                <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
                    <svg class="bi" width="30" height="24"><use xlink:href="#bootstrap"></use></svg>
                </a>
                <span class="mb-3 mb-md-0 text-muted">© 2022 Company, Inc</span>
            </div>

            <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                <li class="ms-3"><a class="text-muted" href="#"><svg class="bi" width="24" height="24"><use xlink:href="#twitter"></use></svg></a></li>
                <li class="ms-3"><a class="text-muted" href="#"><svg class="bi" width="24" height="24"><use xlink:href="#instagram"></use></svg></a></li>
                <li class="ms-3"><a class="text-muted" href="#"><svg class="bi" width="24" height="24"><use xlink:href="#facebook"></use></svg></a></li>
            </ul>
        </footer>
    </div>
</body>

</html>
