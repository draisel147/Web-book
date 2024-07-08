<?php

session_start();

// Include config file
include 'config.php';


// Check if the books in the cart exceed the available stock
$disableBtn = '';
$notification = '';
for ($i = 0; $i <= (int)$_SESSION["intLine"]; $i++) {
    if (($_SESSION["ISBN"][$i]) != "") {
        $isbn = $_SESSION["ISBN"][$i];
        $qty = $_SESSION["strQty"][$i];
        $checkStockQuery = "SELECT Book_Remain FROM book WHERE ISBN = '$isbn'";
        $checkStockResult = $conn->query($checkStockQuery);
        if ($checkStockResult->num_rows > 0) {
            $row = $checkStockResult->fetch_assoc();
            if ($qty > $row["Book_Remain"]) {
                $disableBtn = 'disabled';
                $notification = '<div class="alert alert-danger" role="alert">ไม่สามารถยืนยันคำสั่งซื้อได้ เนื่องจากสินค้าบางรายการมีจำนวนเกินสต็อกที่มีอยู่</div>';
                break;
            }
        }
    }
}

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

// Fetch BookBuyer points
$userID = $_SESSION["User_ID"];
$pointsQuery = "SELECT BBuy_Point FROM book_buyer WHERE BBuy_ID = '$userID'";
$pointsResult = $conn->query($pointsQuery);
// สร้างคำสั่ง SQL เพื่อดึงข้อมูลที่อยู่จากตาราง book_buyer
$addressQuery = "SELECT BBuy_Address FROM book_buyer WHERE BBuy_ID = '$userID'";
$addressResult = $conn->query($addressQuery);

// Check if the query was successful and fetch the points
if ($pointsResult->num_rows > 0) {
    $pointsRow = $pointsResult->fetch_assoc();
    $userPoints = $pointsRow["BBuy_Point"];
} else {
    $userPoints = 0; // Default to 0 if no points found
}

// Display the rest of your HTML code
if (!isset($_SESSION["intLine"]) || !isset($_SESSION["ISBN"])) {
    echo '<script>alert("ไม่พบรายการสินค้าในตะกร้า");</script>';
    echo '<script>window.location.href = "homepage.php";</script>';
    exit(); // Stop further processing
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btn2'])) {
        // รับข้อมูลจากฟอร์ม
        $totalPrice = $_POST['total_price'];
        $payDate = $_POST['pay_date'];
        $payTime = $_POST['pay_time'];

        // เช็คว่าเลือกใช้ที่อยู่ใหม่หรือไม่
        if ($_POST['address_option'] === 'use_new_address') {
            // รับข้อมูลที่อยู่ผู้รับสินค้าใหม่
            // สามารถรับค่าและจัดการตามฟอร์มที่เพิ่มไว้ใน HTML ได้ที่นี่
            $newAddress = $_POST['new_address']; // ตัวอย่างเท่านั้น
        } else {
            // ใช้ที่อยู่ที่สมัครไว้
            $addressRow = $addressResult->fetch_assoc();

            // Check if there is a result
            if ($addressRow) {
                $newAddress = $addressRow['BBuy_Address'];
            } else {
                $newAddress = "No address found";
            }
 // รับค่าที่อยู่ที่สมัครไว้จากฐานข้อมูล
        }

        // จัดการการอัปโหลดไฟล์
        $uploadDir = 'payment/';
        $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['file1']['name']);

        if (move_uploaded_file($_FILES['file1']['tmp_name'], $uploadFile)) {
            // เริ่ม transaction
            $conn->begin_transaction();

            // การอัปโหลดไฟล์สำเร็จ, แทรกข้อมูลลงในตาราง cart
            $cartSQL = "INSERT INTO cart (Mana_ID, BBuy_ID, Total_Price, Buy_Date, Order_Review, Order_Status, Confirmation, Ship_Address)
                        VALUES (?, ?, ?, NOW(), '  ', 'รอตรวจสอบ', ?, ?)";
            $cartStmt = $conn->prepare($cartSQL);
            $cartStmt->bind_param('iiiss', $_SESSION['Mana_ID'], $_SESSION['User_ID'], $totalPrice, $uploadFile, $newAddress);

            if ($cartStmt->execute()) {
                $cartID = mysqli_insert_id($conn);

                // คำสั่ง SQL สำหรับการเพิ่มรายละเอียดการสั่งซื้อลงในตาราง order_detail
                $orderDetailSQL = "INSERT INTO order_detail (Order_ID, Cart_ID, ISBN, Book_Quantity) VALUES (DEFAULT, ?, ?, ?)";
                $orderDetailStmt = $conn->prepare($orderDetailSQL);

                for ($i = 0; $i <= (int)$_SESSION["intLine"]; $i++) {
                    if (($_SESSION["ISBN"][$i]) != "") {
                        $bookQuantity = $_SESSION["strQty"][$i];
                        $orderDetailStmt->bind_param('iii', $cartID, $_SESSION["ISBN"][$i], $bookQuantity);
                        $orderDetailStmt->execute();
                    }
                }

                $orderDetailStmt->close();

                // commit transaction
                $conn->commit();

                unset($_SESSION['intLine']);
                unset($_SESSION['strQty']);
                unset($_SESSION['ISBN']);

                echo '<script>alert("บันทึกข้อมูลการชำระเงินและรายการสั่งซื้อสำเร็จ");</script>';
                echo '<script>window.location.href = "homepage.php";</script>';
            } else {
                // rollback transaction ในกรณีเกิดข้อผิดพลาด
                $conn->rollback();

                echo '<script>alert("เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $cartStmt->error . '");</script>';
            }

            $cartStmt->close();
        } else {
            echo '<script>alert("เกิดข้อผิดพลาดในการอัปโหลดไฟล์");</script>';
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
            position: absolute;
            bottom: 0;
            width: 59%;
        }
    </style>
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
    <br><br>
    <div class="container">
        <form id="form1" method="POST" enctype="multipart/form-data">
            <br>
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
                        <a href="javascript:void(0);" onclick="showPopup();" class="btn btn-outline-success" <?= $disableBtn ?>>ยืนยันคำสั่งซื้อ</a>
                        <?php echo $notification; ?>
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
                                            <label class="col-form-label col-sm-4 mt-2 text-start">ที่อยู่ใหม่</label>
                                            <div class="col-sm-8 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="address_option" id="use_new_address" value="use_new_address" checked>
                                                    <label class="form-check-label" for="use_new_address">
                                                        ใช้ที่อยู่ใหม่
                                                    </label>
                                                </div>
                                                <textarea class="form-control" name="new_address" id="new_address" rows="3"></textarea>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="radio" name="address_option" id="use_registered_address" value="use_registered_address">
                                                    <label class="form-check-label" for="use_registered_address">
                                                        ใช้ที่อยู่ที่สมัครไว้
                                                    </label>
                                                </div>
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
