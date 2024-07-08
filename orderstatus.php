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
    <title>Order Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <link rel="stylesheet" href="style.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<body>
<header data-bs-theme="dark">
<style>
        /* เพิ่ม CSS สำหรับส่วนของรายละเอียดหนังสือ */
        .book-details {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .book-details h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .book-details hr {
            border-top: 2px solid #ddd;
            margin-bottom: 20px;
        }

        .book-details img {
            max-width: 300px;
            height: auto;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .book-details table {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border-collapse: collapse;
        }

        .book-details table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .book-details table .fw-bolder {
            font-weight: bold;
        }

        .book-details .btn-back {
            margin-top: 20px;
        }

        /* เพิ่ม CSS สำหรับส่วนของรีวิว */
        .review {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .review p {
            margin: 5px 0;
        }

        .review .username {
            font-weight: bold;
            color: #007bff;
        }

        .review .score {
            color: #28a745;
            font-weight: bold;
        }

        .review .review-text {
            margin-top: 5px;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        footer {
            margin-top: auto;
        }
    </style>
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

<?php
// ... (previous code)

// Retrieve order information from the cart and cart_items tables
$sql = "SELECT cart.*
        FROM cart
        WHERE cart.BBuy_ID = " . $_SESSION["User_ID"];

$result = $conn->query($sql);

// Check if there are orders for the user
if ($result->num_rows > 0) {
    echo '<div class="container mt-5">';
    
    while ($row = $result->fetch_assoc()) {
        echo '<div class="card mb-3">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">Cart ID: ' . $row['Cart_ID'] . '</h5>';
        echo '<p>Order Status: ' . $row['Order_Status'] . '</p>';
        echo '<p>Total Price: ' . $row['Total_Price'] . '</p>';
        
        // Check if the order is approved
        if ($row['Order_Status'] == 'Approved') {
            // Add a button to view the receipt with JavaScript to open in a new window or popup
            echo '<a href="view_receipt.php?cart_id=' . $row['Cart_ID'] . '" class="btn btn-primary">View Receipt</a>
            ';
            
        }
      
        
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
} else {
    echo "<p class='text-start'>คุณยังไม่มีรายการสั่งซื้อ</p>";
}

// Close the database connection
$conn->close();
?>


    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-eRqNqblF8Vg3mZ8N3SvRMJoPTeBDJjlvL/Jzx6n5CzoI" crossorigin="anonymous"></script>
</body>
<br>
<br>
<br>
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
</html>
