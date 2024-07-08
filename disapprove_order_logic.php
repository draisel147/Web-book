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

        // Update the order status to "Disapproved" in the cart table
        $updateCartStatusSql = "UPDATE cart SET Order_Status = 'Disapproved' WHERE Cart_ID = '$cartID'";
        $conn->query($updateCartStatusSql);

        // You may want to add more logic here if needed

        // Redirect back to the order_admin.php page or wherever you want
        header("location: order_admin.php");
        exit();
    } else {
        echo "Cart ID not provided!";
    }
}
?>
