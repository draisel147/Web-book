<?php
ob_start();
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
            margin-top: auto;
        }
    </style>
</head>

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
                        Logged in : <?= $_SESSION["User_Email"] ?>
                    </span>
                    &nbsp;<a href="logout.php" class="btn btn-outline-danger">Logout</a>&nbsp;&nbsp;
                <?php
                                }

                ?>
                </div>
            </nav>
        </div>
    </header>
    <main>

        <?php
        if (isset($_GET['id'])) {
            $isbn = htmlspecialchars($_GET['id']);
        } else {
            // ถ้าไม่มีค่าตัวระบุ สามารถแสดงข้อความผิดพลาดหรือเรียกกลับไปหน้าหลักได้
            // ตัวอย่างเช่น header("Location: index.php"); exit;
        }
        $query = "SELECT b.*, a.Auth_Name , bc.Cate_Name, sc.SCat_Name, pb.Publ_Name, bt.Type_Name
                  FROM book b 
                  LEFT JOIN author a ON b.Auth_ID = a.Auth_ID 
                  LEFT JOIN category bc ON b.Cate_ID = bc.Cate_ID 
                  LEFT JOIN sub_category sc ON b.SCat_ID = sc.SCat_ID 
                  LEFT JOIN publisher pb ON b.Publ_ID = pb.Publ_ID 
                  LEFT JOIN book_type bt ON b.Type_ID = bt.Type_ID 
                  WHERE b.isbn = '$isbn'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $book = mysqli_fetch_assoc($result);
            $title = $book['Book_Name'];
            $author = $book['Auth_Name'];
            $cate = $book['Cate_Name'];
            $Scat = $book['SCat_Name'];
            $pub = $book['Publ_Name'];
            $type = $book['Type_Name'];
            $BookP = $book['Book_Price'];
        ?>
            <div class="container">
                <div class="book-details">
                    <br>
                    <h1>รายละเอียดหนังสือ</h1>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="image/<?= $book['Book_Image'] ?>" alt="Book Cover">
                        </div>
                        <div class="col-md-8">
                            <table class="table">
                                <tr>
                                    <td class="fw-bolder">ชื่อหนังสือ:</td>
                                    <td><?= $title ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">ชื่อผู้แต่ง:</td>
                                    <td><?= $author ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">ISBN:</td>
                                    <td><?= $isbn ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">ประเภท:</td>
                                    <td><?= $type ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">สำนักพิมพ์:</td>
                                    <td><?= $pub ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">หมวดหมู่หลัก:</td>
                                    <td><?= $cate ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">หมวดหมู่ย่อย:</td>
                                    <td><?= $Scat ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">ราคา:</td>
                                    <td><?= $BookP ?> บาท</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>

        <div class="container mt-3">
            <h2>รีวิว</h2>
            <hr>
            <div class="review">
                <?php
                if (isset($_SESSION["User_ID"])) {
                    $bbuy_id = $_SESSION["User_ID"];
                    $review_query = "SELECT own.*, Book_Buyer.BBuy_Name
                                    FROM own
                                    INNER JOIN Book_Buyer ON own.BBuy_ID = Book_Buyer.BBuy_ID
                                    WHERE own.ISBN = '$isbn' AND own.Book_Score IS NOT NULL AND own.Book_Review IS NOT NULL";
                    $review_result = mysqli_query($conn, $review_query);
                    if (mysqli_num_rows($review_result) > 0) {
                        while ($review_row = mysqli_fetch_assoc($review_result)) {
                            $score = $review_row['Book_Score'];
                            $review = $review_row['Book_Review'];
                            $username = $review_row['BBuy_Name'];
                ?>
                            <div class="review">
                                <p class="username">ผู้รีวิว: <?= $username ?></p>
                                <p class="score">คะแนน: <?= $score ?>/5</p>
                                <p class="review-text">รีวิว: <?= $review ?></p>
                            </div>
                <?php
                        }
                    } else {
                        echo "<p>ยังไม่มีรีวิวสำหรับหนังสือนี้</p>";
                    }
                } else {
                    header("Location: login.php");
                    exit();
                }
                ?>
            </div>
            <div class="container">
                <a href="javascript:history.back()" class="btn btn-primary btn-back float-end">ย้อนกลับ</a>
            </div>
        </div>
        <br>    
        <br>      
    </main>

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
