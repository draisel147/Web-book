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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

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

    <main>
        <br>
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

        <div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-body-tertiary">
            <div class="col-md-6 p-lg-5 mx-auto my-5">
                <h1 class="display-3 fw-bold">BookStore</h1>
                <h3 class="fw-normal text-muted mb-3">The Book is life</h3>
                <!-- <img src="image/photo12064.jpeg" alt="หนังสือ" class="img-fluid" style="max-width: 100%; height: auto;"> -->
            </div>
            <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div>
        </div>




        <div class="container text-center mt-4">
            <div class="row">
                <?php
                    include 'config.php';

                    $query = "SELECT b.*, a.Auth_Name, t.Type_Name 
                            FROM book b 
                            LEFT JOIN author a ON b.Auth_ID = a.Auth_ID 
                            LEFT JOIN book_type t ON b.Type_ID = t.Type_ID 
                            ORDER BY b.ISBN";
                    $result = mysqli_query($conn, $query);

                    if ($result) {
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<div class="col-md-3">';
                                echo '<div class="book card">';
                                echo '<a href="detail.php?id=' . $row['ISBN'] . '"><img src="image/' . $row['Book_Image'] . '" alt="' . $row['Book_Name'] . '" class="card-img-top"></a>';
                                echo '<div class="card-body">';
                                echo '<a href="detail.php?id=' . $row['ISBN'] . '" class="text-dark font-weight-bold"><h5 class="card-title">' . $row['Book_Name'] . '</h5></a>';
                                echo '<p class="card-text">ผู้แต่ง: ' . $row['Auth_Name'] . '</p>';
                                echo '<p class="card-text">ประเภท: ' . $row['Type_Name'] . '</p>';
                                echo '<p class="card-text">ราคา: ' . $row['Book_Price'] . ' บาท</p>';
                                echo '<a href="order.php?id=' . $row['ISBN'] . '" class="btn btn-success">Add to cart</a>';
                                echo '<a href="testread.php?id=' . $row['ISBN']. '" class="btn btn-outline-success m-lg-1 ">ทดลองอ่าน</a>';

                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>ไม่พบหนังสือในรายการ</p>';
                        }
                    } else {
                        echo 'Error: ' . mysqli_error($conn);
                    }

                    mysqli_close($conn);
                ?>

            </div>
        </div> 
        <br>
        <br>
        <br>

    </main>
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
