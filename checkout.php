<?php
session_start();
include 'config.php';

if (!isset($_SESSION["intLine"])) {
    header("location: homepage.php"); // กรณีไม่มีสินค้าในตะกร้า
}

// ตรวจสอบว่ามีสินค้าหรือไม่
if ($_SESSION["intLine"] == 0) {
    header("location: homepage.php");
}

// ตรวจสอบค่าที่รับมาจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ทำตามต้องการต่อไป
    // ...

    // เมื่อทำรายการสั่งซื้อเสร็จสิ้น สามารถลบข้อมูลในตะกร้าได้
    unset($_SESSION["intLine"]);
    unset($_SESSION["ISBN"]);
    unset($_SESSION["strQty"]);

    // ทำการ redirect ไปยังหน้า Order
    header("location: order.php");
    exit(); // อย่าลืมใส่ exit เพื่อหยุดการทำงานของ script ต่อไป
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

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
                            <a href="cart.php" class="btn btn-primary">ดูตะกร้าสินค้า</a>
                        </li>
                    </ul>
                    <form class="d-flex" role="search" method="get" action="search.php">
                        <input class="form-control me-2" type="search" placeholder="Search" name="q" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>&nbsp;
                    </form>
                    &nbsp;&nbsp;<?php if (isset($_SESSION["BBuy_ID"]) && isset($_SESSION["BBuy_Name"]) && isset($_SESSION["Email"])) { ?>
                        <span class="navbar-text me-3">
                            Logged in : <?= $_SESSION["Email"] ?>
                        </span>
                        &nbsp;<a href="logout.php" class="btn btn-outline-danger">Logout</a>&nbsp;&nbsp;
                    <?php } ?>
                </div>
            </nav>
        </div>
    </header>

    <br><br>
    <div class="col-md-10">
        <div class="alert alert-success h4" role="alert">
            ยืนยันการสั่งซื้อ
        </div>

        <!-- แสดงรายละเอียดสินค้าที่มีในตะกร้า -->
        <form method="post" action="order_admin.php">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ชื่อหนังสือ</th>
                        <th>ราคา</th>
                        <th>จำนวน</th>
                        <th>รวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalPrice = 0;
                    foreach ($_SESSION["ISBN"] as $index => $isbn) {
                        if (!empty($isbn)) {
                            $sql = "SELECT * FROM book WHERE ISBN = '$isbn'";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_assoc($result);

                            $quantity = $_SESSION["strQty"][$index];
                            $price = $row["Book_Price"];
                            $subtotal = $quantity * $price;
                            $totalPrice += $subtotal;
                    ?>
                            <tr>
                                <td>
                                    <div class="float-right">
                                        <img src="image/<?= $row["Book_Image"] ?>" width="80px" height="100px" class="border">
                                        <?= $row["Book_Name"] ?>
                                    </div> <br>
                                </td>
                                <td><?= $price ?></td>
                                <td><?= $quantity ?></td>
                                <td><?= $subtotal ?></td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <!-- ส่วนอื่น ๆ ของฟอร์ม -->
            <div class="row mb-3">
                <div class="alert alert-info h4" role="alert">
                    ราคารวมทั้งหมด: <?= $totalPrice ?> บาท
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputName" class="col-sm-2 col-form-label">ชื่อ-นามสกุล</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputName" name="customer_name" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputEmail" class="col-sm-2 col-form-label">อีเมล</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="inputEmail" name="customer_email" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputAddress" class="col-sm-2 col-form-label">ที่อยู่</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="inputAddress" name="customer_address" rows="3" required></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputPayment" class="col-sm-2 col-form-label">วิธีการชำระเงิน</label>
                <div class="col-sm-10">
                    <select class="form-select" id="inputPayment" name="payment_method" required>
                        <option value="credit_card">บัตรเครดิต</option>
                        <option value="bank_transfer">โอนเงินผ่านธนาคาร</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputNote" class="col-sm-2 col-form-label">หมายเหตุ</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="inputNote" name="order_note" rows="3"></textarea>
                </div>
            </div>

            <div style="text-align:right">
                <button type="submit" class="btn btn-outline-success">ยืนยัน</button>
            </div>
        </form>
    </div>

</body>

</html>
