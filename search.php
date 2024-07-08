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
                            Logged in : <?= $_SESSION["User_Email"] ?>
                        </span>
                        &nbsp;<a href="logout.php" class="btn btn-outline-danger">Logout</a>&nbsp;&nbsp;
                    <?php } ?>
                </div>
            </nav>
        </div>
    </header>
    <div class="container text-center mt-4">
            <div class="row">
            <?php
            // Include config file
            include 'config.php';

            // Check if the search query parameter is set
            if (isset($_GET["q"])) {
                $search_query = $_GET["q"];

                // Perform the search query
                $query = "SELECT b.*, a.Auth_Name, t.Type_Name 
                        FROM book b 
                        LEFT JOIN author a ON b.Auth_ID = a.Auth_ID 
                        LEFT JOIN book_type t ON b.Type_ID = t.Type_ID 
                        WHERE b.Book_Name LIKE '%$search_query%' OR a.Auth_Name LIKE '%$search_query%' OR t.Type_Name LIKE '%$search_query%'
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

                // Free result set
                mysqli_free_result($result);
            } else {
                // If no search query is provided, show all books
                $query_all_books = "SELECT b.*, a.Auth_Name, t.Type_Name 
                                    FROM book b 
                                    LEFT JOIN author a ON b.Auth_ID = a.Auth_ID 
                                    LEFT JOIN book_type t ON b.Type_ID = t.Type_ID 
                                    ORDER BY b.ISBN";
                $result_all_books = mysqli_query($conn, $query_all_books);

                if ($result_all_books) {
                    if (mysqli_num_rows($result_all_books) > 0) {
                        while ($row = mysqli_fetch_assoc($result_all_books)) {
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

                // Free result set
                mysqli_free_result($result_all_books);
            }

            // Close connection
            mysqli_close($conn);
            ?>
            </div>
        </div>

    </main>

    <footer>
        <!-- ... (เนื้อหาเดิม) ... -->
    </footer>

</body>

</html>
