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

        // Check if there are rows in the result set
        if ($orderDetailsResult->num_rows > 0) {
            // Calculate total price and BookBuyer ID
            $totalPrice = 0;
            $bookBuyerID = null;

            while ($orderDetailRow = $orderDetailsResult->fetch_assoc()) {
                $totalPrice += $orderDetailRow["Total_Price"];
                $bookBuyerID = $orderDetailRow["BBuy_ID"];
                $isbn = $orderDetailRow["ISBN"];
                $quantity = $orderDetailRow["Book_Quantity"];

                // Insert book details into own table
                $insertOwnSql = "INSERT INTO own (BBuy_ID, ISBN) VALUES ('$bookBuyerID', '$isbn')";
                
                // Execute the query
                $conn->query($insertOwnSql);

                // Reduce book_quantity if Book_Type is eBook (Book_Type=1)
                if ($orderDetailRow["Type_ID"] == 1) {
                    $reduceQuantitySql = "UPDATE book SET Book_Remain = Book_Remain - '$quantity' WHERE ISBN = '$isbn'";
                    $conn->query($reduceQuantitySql);
                }
            }

            // Update the order status to "Approved" in the cart table
            $updateCartStatusSql = "UPDATE cart SET Order_Status = 'Approved' WHERE Cart_ID = '$cartID'";
            $conn->query($updateCartStatusSql);

            // Calculate 10% of the total price and update points for BookBuyer
            $pointsEarned = $totalPrice * 0.1;
            $updatePointsSql = "UPDATE book_buyer SET BBuy_point = BBuy_point + '$pointsEarned' WHERE BBuy_ID = '$bookBuyerID'";
            $conn->query($updatePointsSql);

            // Redirect back to the order_admin.php page or wherever you want
            header("location: order_admin.php");
            exit();
        } else {
            echo "No rows found in the result set!";
        }
    } else {
        echo "Cart ID not provided!";
    }
}
?>
