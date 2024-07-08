<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <?php
    if (isset($_GET['cart_id'])) {
        $cart_id = htmlspecialchars($_GET['cart_id']);
    } else {
        // ถ้าไม่มีค่าตัวระบุ สามารถแสดงข้อความผิดพลาดหรือเรียกกลับไปหน้าหลักได้
        // ตัวอย่างเช่น header("Location: index.php"); exit;
    }

    $query = "SELECT od.*, cr.Order_Status, cr.Buy_Date, cr.Mana_ID, b.ISBN, b.Book_Name, b.Book_Price, cr.BBuy_ID
    FROM order_detail od
    LEFT JOIN cart cr ON od.Cart_ID = cr.Cart_ID
    LEFT JOIN book b ON od.ISBN = b.ISBN
    WHERE od.cart_id = '$cart_id'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $totalPrice = 0; // สร้างตัวแปรเพื่อเก็บราคารวมทั้งหมด
        $orderId = "";
        $orderStatus = "";
        $buyDate = "";
        $manaId = "";
        $buyer = "";

        ?>
        <div class="container">
            <div class="row">
                <div class="col-md">
                    <div class="alert alert-primary h4 text-center mt-4" role="alert">
                        BookStore
                    </div>
                </div>
            </div>
        </div>
        <?php

        // เรียกข้อมูลการสั่งซื้อและแสดงผล
        while ($order = mysqli_fetch_assoc($result)) {
            if ($orderId === "") {
                $orderId = $order['Order_ID'];
                $orderStatus = $order['Order_Status'];
                $buyDate = $order['Buy_Date'];
                $manaId = $order['Mana_ID'];
                $buyer = $order['BBuy_ID'];
                ?>
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <p>เรียนท่านผู้มีอุปการะคุณ</p><br>
                            <p>ร้านหนังสือของเรา ขอขอบพระคุณท่านที่ได้ใช้งานการสั่งซื้อที่ WWW.Book-store.com ขณะนี้ เราได้รับหลักฐานการชำระเงิน สำหรับการสั่งซื้อเลขที่ <b><?= $orderId ?></b> จากท่านเรียบร้อยแล้ว ทางร้านของเราขอขอบคุณที่อุดหนุนและเชื่อใจร้านของเรา</p>
                            <p>สถานะการสั่งซื้อ : <b><?= $orderStatus ?></b></p>
                            <p>วิธีการชำระเงิน :<b> ชำระเงินผ่าน QR Code (Internet/Mobile Banking)</b></p>
                            <p>Order No : <b><?= $orderId ?></b> </p>
                            <p>วันที่ซื้อ : <b><?= $buyDate ?></b></p>
                            <p>ข้อมูลร้านค้า :<b><?= $manaId ?></b></p>
                        </div>
                    </div>
                </div>
                <?php
            }

            $totalPrice += $order['Book_Price'] * $order['Book_Quantity'];
        }
        ?>
        <div class="container">
            <div class="card mb-4 mt-4">
                <div class="card-body">
                    <div class="container">
                        <p>รายละเอียดการสั่งซื้อ</p>
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th>ISBN</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>ราคา/หน่วย</th>
                                    <th>จำนวน</th>
                                    <th>ราคา</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                mysqli_data_seek($result, 0); // ให้ cursor อยู่ที่แถวแรกอีกครั้งเพื่อเริ่มการ loop ใหม่
                                while ($order = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?= $order['ISBN'] ?></td>
                                        <td><?= $order['Book_Name'] ?></td>
                                        <td><?= $order['Book_Price'] ?></td>
                                        <td><?= $order['Book_Quantity'] ?></td>
                                        <td><?= $order['Book_Price'] * $order['Book_Quantity'] ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="4" class="text-end">รวมเป็นเงิน</td>
                                    <td class="text-center"><?= number_format($totalPrice, 2) ?></td>
                                    <td>บาท</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <p>ข้อมูลลูกค้า : <?= $buyer ?></p>
        </div>
        <?php
    }
    ?>
    <footer class="container text-center">
        <p>หากท่านมีข้อสงสัยประการใดหรือต้องการสอบถามข้อมูลเพิ่มเติม กรุณาติดต่อที่ WWW.Book-store.com</p>
        <div>
            <a href="orderstatus.php" class="btn btn-success print-button">ย้อนกลับ</a>
            <button onclick="window.print()" href="" class="btn btn-success print-button">พิมพ์ใบเสร็จ</button>
        </div>
    </footer>
</body>

</html>
