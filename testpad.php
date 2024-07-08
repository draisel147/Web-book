<?php
session_start();

include 'config.php';

if (!isset($_SESSION["User_ID"])) {
    header("location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if Cart ID is provided in the URL
    if (isset($_GET['cart_id'])) {
        $cartID = $_GET['cart_id'];

        // Fetch order details, book details, and cart details
        $orderDetailsSql = "SELECT order_detail.*, book.ISBN, book.*, cart.* 
                            FROM order_detail 
                            INNER JOIN book ON order_detail.ISBN = book.ISBN 
                            INNER JOIN cart ON order_detail.Cart_ID = cart.Cart_ID 
                            WHERE order_detail.Cart_ID = '$cartID'";

        $orderDetailsResult = $conn->query($orderDetailsSql);

        // Calculate total price and BookBuyer ID
        $totalPrice = 0;
        $bookBuyerID = $orderDetailsResult["BBuy_ID"];

        while ($orderDetailRow = $orderDetailsResult->fetch_assoc()) {
            $totalPrice += $orderDetailRow["Price"];
            $bookBuyerID = $orderDetailRow["BBuy_ID"];
        }

        // Update the order status to "Approved" in the cart table
        $updateCartStatusSql = "UPDATE cart SET Order_Status = 'Approved' WHERE Cart_ID = '$cartID'";
        $conn->query($updateCartStatusSql);

        // Insert book details into own table
        while ($orderDetailRow = $orderDetailsResult->fetch_assoc()) {
            $isbn = $orderDetailRow["ISBN"];
            $bbuyId = $orderDetailRow["BBuy_ID"];

            // Use appropriate column names based on your 'own' table structure
            $insertOwnSql = "INSERT INTO own (BBuy_ID, ISBN) VALUES ('$bbuyId', '$isbn')";
            
            // Execute the query
            $conn->query($insertOwnSql);
        }

        // Calculate 10% of the total price and update points for BookBuyer
        $pointsEarned = $totalPrice * 0.1;
        $updatePointsSql = "UPDATE bookbuyer SET points = points + '$pointsEarned' WHERE BBuy_ID = '$bookBuyerID'";
        $conn->query($updatePointsSql);

        // Redirect back to the order_admin.php page or wherever you want
        header("location: order_admin.php");
        exit();
    } else {
        echo "Cart ID not provided!";
    }
}
?>
