<?php
session_start();

// Include config file
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["User_ID"])) {
    // If not logged in, redirect to the login page
    header("location: login.php");
    exit();
}

function generateBarcode($trackingNumber)
{
    // URL ของเว็บไซต์ไปรษณีย์ไทยสำหรับติดตามพัสดุ
    $url = "https://track.thailandpost.co.th/?trackNumber=$trackingNumber";

    // สร้างรหัส HTML สำหรับแสดงบาร์โค้ด
    $barcodeHtml = "<img src='https://barcode.tec-it.com/barcode.ashx?data=$trackingNumber&code=Code128&dpi=96' alt='Barcode'>";

    // คืนค่า HTML ของบาร์โค้ดพร้อมลิงก์สำหรับติดตามพัสดุ
    return "<a href='$url' target='_blank'>$barcodeHtml</a>";
}

// Logout logic
if (isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("location: login.php");
    exit();
}

// Check if Cart ID is provided in the URL
// Check if Cart ID is provided in the URL
if (isset($_GET['cart_id'])) {
    $cartID = $_GET['cart_id'];

    // Fetch the order details from the database based on Cart ID
    $sql = "SELECT cart.*, book_buyer.*, book_buyer.BBuy_Name, book_buyer.BBuy_LName 
    FROM cart 
    INNER JOIN book_buyer ON cart.BBuy_ID = book_buyer.BBuy_ID
    WHERE Cart_ID = '$cartID'";


    $query = "SELECT od.*, cr.Order_Status, cr.Buy_Date, cr.Mana_ID, b.ISBN, b.Book_Name, b.Book_Price, cr.BBuy_ID
    FROM order_detail od
    LEFT JOIN cart cr ON od.Cart_ID = cr.Cart_ID
    LEFT JOIN book b ON od.ISBN = b.ISBN
    WHERE od.cart_id = '$cartID'";

    $orderDetailRow = mysqli_query($conn, $query);

    $result = $conn->query($sql);

    // Initialize orderId
    $orderId = "";

    if ($result->num_rows == 1) {
        $cartData = $result->fetch_assoc();


        // Fetch order details related to the cart from Order Detail table
        $orderDetailsSql = "SELECT order_detail.*, book.ISBN, book.*, book_buyer.*, book_buyer.BBuy_Name, cart.Cart_ID
        FROM order_detail
        INNER JOIN book ON order_detail.ISBN = book.ISBN
        INNER JOIN cart ON order_detail.Cart_ID = cart.Cart_ID
        INNER JOIN book_buyer ON cart.BBuy_ID = book_buyer.BBuy_ID
        WHERE order_detail.Cart_ID = '$cartID' AND book.Type_ID = '1'";

        $orderDetailsResult = $conn->query($orderDetailsSql);

        // เรียกใช้ฟังก์ชัน generateBarcode() เพื่อสร้างรูปภาพบาร์โค้ด
        $barcode = generateBarcode($cartData['Tracking_ID']);
        
    } else {
        // If no order found, you may want to handle this case
        echo "Order not found!";
        exit();
    }
} else {
    // If Cart ID is not provided in the URL, redirect to order_admin.php
    header("location: order_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จ่าหน้าซอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* Custom CSS */
        .envelope {
            background-color: #fff;
            width: 400px;
            padding: 20px;
            border: 2px solid #000;
            border-radius: 10px;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .envelope .address {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .envelope .sender {
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <?php

    // เรียกข้อมูลการสั่งซื้อและแสดงผล
    while ($order = mysqli_fetch_assoc($orderDetailRow)) {
        if ($orderId === "") {
            $orderId = $order['Order_ID'];
            $orderStatus = $order['Order_Status'];
            $buyDate = $order['Buy_Date'];
            $manaId = $order['Mana_ID'];
            $buyer = $order['BBuy_ID'];
        }
    }
    ?>


    <div class=" container text-center ">
        <?php echo $barcode; ?>
    </div>
    <div class="container">
        <div class="envelope">

            <div class="sender">
                <p>จาก: Bookstore</p>
                <p>ที่อยู่: 238-226 เสรี 9 ซอย 9 แขวงสวนหลวง เขตสวนหลวง กรุงเทพมหานคร 10250</p>
            </div>
            <hr>
            <div class="recipient">
                <p>ถึง: <?php echo $cartData['BBuy_Name'] . ' ' . $cartData['BBuy_LName']; ?></p>

                <p>ที่อยู่: <?php echo $cartData['Ship_Address']; ?></p>


            </div>
        </div>
    </div>
    <div class="container">
        <div class="card mb-4 mt-4">
            <div class="card-body">
                <div class="container">
                    <p>รายละเอียดการสั่งซื้อ</p>
                    <table class='table table-striped table-bordered'>
                        <thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Book Title</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalQuantity = 0; // เก็บจำนวนเล่มทั้งหมด
                            while ($orderDetailRow = $orderDetailsResult->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $orderDetailRow["ISBN"] . "</td>
                                        <td>" . $orderDetailRow["Book_Name"] . "</td>
                                        <td class='text-end'>" . $orderDetailRow["Book_Quantity"] . "</td>
                                    </tr>";
                                $totalQuantity += $orderDetailRow["Book_Quantity"]; // เพิ่มจำนวนเล่มของหนังสือนี้เข้าไปในรวม
                            }
                            ?>
                            <tr>
                                <td colspan="2" class="text-end">Qty Total</td>
                                <td class="text-end"><?php echo number_format($totalQuantity); ?></td>
                            </tr>
                        </tbody>

                    </table>
                    <hr>
                    <p class=" text-end ">Order ID:<b><?= $orderId ?></b></p>

                </div>
            </div>

        </div>
        <button onclick="window.print()" href="" class="btn btn-success print-button">พิมพ์ใบเสร็จ</button>
    </div>
</body>

</html>