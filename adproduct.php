<?php
session_start();

// Include config file
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["User_ID"])) {
   // If not logged in, redirect to the login page
    header("location: login.php");
    exit(); // Ensure that the script stops here to prevent further execution
}
// Logout logic
if (isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("location: login.php");
    exit();
}

// Display the rest of your HTML code
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เพิ่มข้อมูลหนังสือ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
        /* เพิ่มสไตล์เพื่อลดขนาดฟอร์มลง 50% */
        form {
            width: 50%;
            margin: auto;
        }
    </style>
</head>

<body>
<div class="container-fluid">
        <header class="d-flex flex-wrap justify-content-between py-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
                <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
                <span class="fs-4">Admin</span>
            </a>

            <ul class="nav nav-pills">
                <li class="nav-item"><a href="admin.php" class="nav-link active" aria-current="page">Home</a></li>
                <li class="nav-item"><a href="order_admin.php" class="nav-link">Order</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link">ยอดขาย</a></li>
                <li class="nav-item"><a href="#" class="nav-link">สถิติผู้ซื้อ</a></li>
            </ul>

            <?php if (isset($_SESSION["User_ID"]) && isset($_SESSION["User_Name"]) && isset($_SESSION["User_Email"])) { ?>
                <span class="navbar-text me-3">
                    Logged in : <?= $_SESSION["User_Email"] ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>&nbsp;&nbsp;
            <?php } ?>
        </header>
    </div>
    <div class="mt-4 ms-auto">
        <?php
        if (isset($_SESSION["User_ID"]) && isset($_SESSION["User_Name"]) && isset($_SESSION["User_Email"])) {
            echo "<h5 class='text-start'>ยินดีต้อนรับ " . $_SESSION["User_Name"] . "!</h5>";
        } else {
            echo "<h2 class='text-start'>ยินดีต้อนรับ ผู้เยี่ยมชม!</h2>";
            echo "<p class='text-start'>กรุณาเข้าสู่ระบบเพื่อดูเนื้อหา.</p>";
        }
        ?>
    </div>

    <h2 class="text-center">เพิ่มข้อมูลหนังสือ</h2>

    <?php
    include 'config.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $isbn = $_POST["isbn"];
        $book_title = $_POST["book_title"];
        $price = $_POST["price"];
        $added_date = $_POST["added_date"];
        $quantity = $_POST["quantity"];
        $publisher_id = $_POST["publisher_id"];
        $category_id = $_POST["category_id"];
        $main_category_id = $_POST["main_category_id"];
        $sub_category_id = $_POST["sub_category_id"];
        $author_id = $_POST["author_id"];

        // เช็คว่าเลข ISBN ซ้ำหรือไม่
        $check_duplicate = "SELECT COUNT(*) as count FROM Book WHERE ISBN = '$isbn'";
        $result = $conn->query($check_duplicate);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            echo '<div class="alert alert-danger text-center" role="alert">Error: เลข ISBN นี้มีอยู่ในระบบแล้ว</div>';
        } else {
            // ตรวจสอบไฟล์ที่อัปโหลด
            if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $image_name = $_FILES['image']['name'];
                $image_tmp = $_FILES['image']['tmp_name'];

                // บันทึกรูปภาพไปยังโฟลเดอร์ที่กำหนด
                move_uploaded_file($image_tmp, "img/" . $image_name);
            } else {
                echo '<div class="alert alert-danger text-center" role="alert">Error: ไม่สามารถอัปโหลดรูปภาพ</div>';
                exit();
            }

            $sql = "INSERT INTO book (ISBN, Book_Name, Book_Price, Add_Date, Book_Remain, Publ_ID, Auth_ID, Type_ID, Cate_ID, SCat_ID, View_Count, Book_Image) 
                        VALUES ('$isbn', '$book_title', $price, '$added_date', $quantity, $publisher_id, $author_id, $category_id, $main_category_id, $sub_category_id, 0, '$image_name')";

            if ($conn->query($sql) === TRUE) {
                echo '<div class="alert alert-success text-center" role="alert">เพิ่มข้อมูลสำเร็จ!</div>';
                echo '<a href="show_product.php" class="btn btn-success">ดูข้อมูลหนังสือ</a>';
                // เปลี่ยนทางไปยังหน้า show_product.php
                header("Location: show_product.php");
                exit(); // จบการทำงานของสคริปต์
            } else {
                echo '<div class="alert alert-danger text-center" role="alert">Error: ' . $conn->error . '</div>';
            }
        }
    }
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="isbn" class="form-label">ISBN:</label>
            <input type="text" name="isbn" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="book_title" class="form-label">ชื่อหนังสือ:</label>
            <input type="text" name="book_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">ราคา:</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="added_date" class="form-label">วันที่เพิ่ม:</label>
            <input type="date" name="added_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">จำนวนหนังสือ:</label>
            <input type="number" name="quantity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="publisher_id" class="form-label">สำนักพิมพ์:</label>
            <select name="publisher_id" class="form-select" required>
                <?php
                $query = "SELECT * FROM publisher";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=\"{$row['Publ_ID']}\">{$row['Publ_Name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">ประเภทหนังสือ:</label>
            <select name="category_id" class="form-select" required>
                <?php
                $query = "SELECT * FROM book_type";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=\"{$row['Type_ID']}\">{$row['Type_Name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="main_category_id" class="form-label">หมวดหมู่:</label>
            <select name="main_category_id" class="form-select" required>
                <?php
                $query = "SELECT * FROM category";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=\"{$row['Cate_ID']}\">{$row['Cate_Name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="sub_category_id" class="form-label">หมวดหมู่ย่อย:</label>
            <select name="sub_category_id" class="form-select" required>
                <?php
                $query = "SELECT * FROM sub_category";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=\"{$row['SCat_ID']}\">{$row['SCat_Name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="author_id" class="form-label">ผู้แต่ง:</label>
            <select name="author_id" class="form-select" required>
                <?php
                $query = "SELECT * FROM author";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=\"{$row['Auth_ID']}\">{$row['Auth_Name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">รูปภาพ:</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">เพิ่มข้อมูล</button>
        <a href="admin.php" class="btn btn-success">ดูข้อมูลหนังสือ</a>
    </form>

    <!-- เพิ่มลิงก์ไปยังไฟล์ JavaScript ของ Bootstrap 5 ที่ต้องเรียกหลังจาก jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy6nA+2N7uJFmDp4RpuC7t7IXflAzHawZ" crossorigin="anonymous"></script>
</body>

</html>
