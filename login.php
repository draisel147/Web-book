<?php
session_start();
include 'config.php';
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>

    <!-- เพิ่ม Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">   
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 100px;
        }

        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 rounded">
                <h5 class="text-center">Login</h5>
                <form method="POST" action="login_chck.php">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" required placeholder="Email">
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" required placeholder="Password">
                    </div>

                    <?php
                    if (isset($_SESSION["Error"])) {
                        echo "<div class='text-danger text-center mb-3'> ";
                        echo $_SESSION["Error"];
                        echo "</div>";
                        unset($_SESSION["Error"]); // ล้างตัวแปร session หลังจากที่แสดงค่า
                    }
                    ?>

                    <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
                </form>
            </div>
        </div>
        <div class="row text-center mt-3">
            <a href="register.php"> Register </a>
        </div>
    </div>   

    <!-- เพิ่ม Bootstrap JS และ Popper.js (ถ้าใช้ Dropdown) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
