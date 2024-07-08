<?php
session_start();
include 'config.php';

// Check if the shopping cart is not empty
if (!isset($_SESSION["intLine"]) || !isset($_SESSION["ISBN"])) {
    echo '<script>alert("ไม่พบรายการสินค้าในตะกร้า");</script>';
    echo '<script>window.location.href = "homepage.php";</script>';
    exit(); // Stop further processing
}

// Continue with processing the shopping cart
for ($i = 0; $i <= (int)$_SESSION["intLine"]; $i++) {
    if (isset($_SESSION["ISBN"][$i])) {
        $bookQuantity = isset($_SESSION["strQty"][$i]) ? (int)$_SESSION["strQty"][$i] : 0;

        // Check if bookQuantity is greater than 0 before processing
        if ($bookQuantity > 0) {
            $sql1 = "SELECT b.*, t.Type_Name 
                    FROM book b 
                    JOIN book_type t ON b.Type_ID = t.Type_ID 
                    WHERE ISBN = '" . $_SESSION["ISBN"][$i] . "'";
            $result1 = mysqli_query($conn, $sql1);

            // Check if the query was successful before fetching data
            if ($result1) {
                $row_book = mysqli_fetch_array($result1);

                // Your existing code for processing the shopping cart goes here
                $sum = $bookQuantity * $row_book['Book_Price'];

                // Get Cart_ID from the cart insertion
                $cartID = /* Get the Cart_ID from the previous cart insertion operation */;

                // Example: Insert data into order_detail table
                $orderDetailSQL = "INSERT INTO order_detail (Order_ID, Cart_ID, ISBN, Book_Quantity) VALUES (DEFAULT, ?, ?, ?)";
                $orderDetailStmt = $conn->prepare($orderDetailSQL);
                $orderDetailStmt->bind_param('iii', $cartID, $_SESSION["ISBN"][$i], $bookQuantity);
                $orderDetailStmt->execute();
            } else {
                // Handle query error
                echo '<script>alert("Error in fetching book details: ' . mysqli_error($conn) . '");</script>';
                echo '<script>window.location.href = "homepage.php";</script>';
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
<body>
<header data-bs-theme="dark">
        <div class="container mt-3">
            <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
                <a class="navbar-brand" href="index.html">Book Store</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="homepage.php">หน้าหลัก</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="orderstatus.php">สถานะการสั่งซื้อ</a>
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
    <br><br>
    <div class="container">
        <form id="form1" method="POST">
            <div class="row">
                <div class="col-md-10">
                    <div class="alert alert-success h4" role="alert">
                        รายการสั่งซื้อสินค้า
                    </div>
                    <table class="table table-hover">
                        <tr>
                            <th>ชิ้นที่</th>
                            <th>ชื่อหนังสือ</th>
                            <th>ราคา</th>
                            <th>จำนวน</th>
                            <th>ประเภทหนังสือ</th>
                            <th>เพิ่ม - ลด</th>
                            <th>ลบรายการ</th>
                        </tr>
                        <?php
                        $Total = 0;
                        $sumPrice = 0;
                        $n = 1;   //ตัวแปรนับลำดับ
                        for ($i = 0; $i <= (int)$_SESSION["intLine"]; $i++) {
                            if (($_SESSION["ISBN"][$i]) != "") {
                                $sql1 = "SELECT b.*, t.Type_Name 
                                    FROM book b 
                                    JOIN book_type t ON b.Type_ID = t.Type_ID 
                                    WHERE ISBN = '" . $_SESSION["ISBN"][$i] . "'";
                                $result1 = mysqli_query($conn, $sql1);
                                $row_book = mysqli_fetch_array($result1);

                                $_SESSION["price"] = $row_book['Book_Price'];
                                $Total = $_SESSION["strQty"][$i];
                                $sum = $Total * $row_book['Book_Price'];
                                $sumPrice = $sumPrice + $sum;
                        ?>
                                <tr>
                                    <td><?= $n ?></td>
                                    <td>
                                        <div class="float-right">
                                            <img src="image/<?= $row_book['Book_Image'] ?>" width="80px" height="100px" class="border">
                                            <?= $row_book['Book_Name'] ?>
                                        </div> <br>
                                    </td>
                                    <td><?= $row_book['Book_Price'] ?></td>
                                    <td><?= $_SESSION["strQty"][$i] ?></td>
                                    <td><?= $row_book['Type_Name'] ?></td>
                                    <td>
                                        <a href="order.php?id=<?= $row_book['ISBN'] ?>" class="btn btn-outline-info">+</a>
                                        <?php
                                        if ($_SESSION["strQty"][$i] > 1) { ?>
                                            <a href="order_del.php?id=<?= $row_book['ISBN'] ?>" class="btn btn-outline-danger">-</a>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td><a href="del.php?Line=<?= $i ?>"><button type="button" class="btn btn-danger">x</button></a> </td>
                                </tr>
                        <?php
                                $n++;
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="4" class="text-end">รวมเป็นเงิน</td>
                            <td class="text-center"><?= number_format($sumPrice, 2) ?></td>
                            <td>บาท</td>
                        </tr>
                    </table>
                    <div style="text-align:right">
                        <a href="homepage.php"> <button type="button" class="btn btn-outline-secondary">เลือกซื้อสินค้าต่อ</button></a>
                        <a href="javascript:void(0);" onclick="showPopup();" class="btn btn-outline-success">ยืนยันคำสั่งซื้อ</a>
                    </div>
                    <script>
                        function showPopup() {
                            // สร้าง Overlay
                            var overlay = document.createElement('div');
                            overlay.className = 'overlay';

                            // สร้าง Popup
                            var popup = document.createElement('div');
                            popup.className = 'popup container';
                            popup.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h2 class="m-0">แจ้งการโอนเงิน (ส่งสลิป)</h2>
                                    <span class="close" onclick="closePopup();" style="cursor: pointer; color: silver;">&times;</span>
                                </div>
                                <div class="float-start me-3 row">
                                    <img src="image/banki.png">
                                    <div class="float-end col">
                                        <h3>ธ.กสิกรไทย</h3>
                                        <p>Book store 113-3-25231-9</p>
                                    </div>
                                </div>
                                <div class="float-end h2">
                                    <p>ยอดเงินที่ต้องชำระ: <?= number_format($sumPrice, 2) ?> บาท</p>
                                </div>
                                <br><br><br><br>
                                <div class="col">
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="row">
                                            <label class="col-form-label col-sm-4 mt-2 text-start">จำนวนเงิน</label>
                                            <div class="col-sm-8 mt-2">
                                                <input type="float" class="form-control" name="total_price" required placeholder="ยอดเงินที่โอน">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-form-label col-sm-4 mt-2 text-start">วันที่โอน</label>
                                            <div class="col-sm-8 mt-2">
                                                <input type="date" class="form-control" name="pay_date" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-form-label col-sm-4 mt-2 text-start">เวลาที่โอน</label>
                                            <div class="col-sm-8 mt-2">
                                                <input type="time" class="form-control" name="pay_time" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-form-label col-sm-4 mt-2 text-start">หลักฐานการชำระเงิน</label>
                                            <div class="col-sm-8 mt-2">
                                                <input type="file" class="form-control" name="file1" required><br>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="submit" name="btn2" class="btn btn-primary">Submit</button>
                                                <button type="button" class="btn btn-secondary" onclick="closePopup();">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            `;

                            // เพิ่ม Popup และ Overlay เข้าไปใน body
                            document.body.appendChild(overlay);
                            overlay.appendChild(popup);
                        }

                        function closePopup() {
                            // ลบ Overlay และ Popup
                            var overlay = document.querySelector('.overlay');
                            overlay.parentNode.removeChild(overlay);
                        }
                    </script>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
