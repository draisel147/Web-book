<?php
session_start();
// เชื่อมต่อกับไฟล์ config.php เพื่อใช้ค่าตัวแปรสำหรับการเชื่อมต่อฐานข้อมูล
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">รีวิวของคุณถูกส่งเรียบร้อยแล้ว!</h4>
            <p>ขอบคุณสำหรับการรีวิวและให้คะแนนหนังสือ ความคิดเห็นของคุณมีค่าอย่างมากต่อเรา</p>
            <hr>
            <p class="mb-0">กลับสู่หน้าหลัก <a href="homepage.php">ที่นี่</a></p>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-eRqNqblF8Vg3mZ8N3SvRMJoPTeBDJjlvL/Jzx6n5CzoI" crossorigin="anonymous"></script>
</body>

</html>
