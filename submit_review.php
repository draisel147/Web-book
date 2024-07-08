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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isbn = $_POST["isbn"];
    $score = $_POST["score"];
    $review = $_POST["review"];
    $userID = $_SESSION["User_ID"];

    // Check if the user has already submitted a review for the book
    $existingReviewQuery = "SELECT * FROM own WHERE BBuy_ID = '$userID' AND ISBN = '$isbn'";
    $existingReviewResult = $conn->query($existingReviewQuery);

    if ($existingReviewResult->num_rows > 0) {
        // If a review exists, update the existing review
        $updateReviewQuery = "UPDATE own SET Book_Score = '$score', Book_Review = '$review' WHERE BBuy_ID = '$userID' AND ISBN = '$isbn'";
        if ($conn->query($updateReviewQuery) === TRUE) {
            echo "Review updated successfully.";
        } else {
            echo "Error updating review: " . $conn->error;
        }
    } else {
        // If a review doesn't exist, insert a new review
        $insertReviewQuery = "INSERT INTO own (BBuy_ID, ISBN, Book_Score, Book_Review) VALUES ('$userID', '$isbn', '$score', '$review')";
        if ($conn->query($insertReviewQuery) === TRUE) {
            echo "Review submitted successfully.";
        } else {
            echo "Error submitting review: " . $conn->error;
        }
    }
}
?>
