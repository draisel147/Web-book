<?php
ob_start();
session_start();
include 'config.php';
if(isset($_GET['id'])) {
    $isbn = htmlspecialchars($_GET['id']);
} else {
    // ถ้าไม่มีค่าตัวระบุ สามารถแสดงข้อความผิดพลาดหรือเรียกกลับไปหน้าหลักได้
    // ตัวอย่างเช่น header("Location: index.php"); exit;
}
$query = "SELECT b.*, a.Auth_Name 
          FROM book b 
          LEFT JOIN author a ON b.Auth_ID = a.Auth_ID 
          WHERE b.isbn = '$isbn'";
$result = mysqli_query($conn, $query);
// ตรวจสอบว่ามีค่า BBuy_ID ใน session หรือไม่
if(isset($_SESSION["User_ID"])) {
    $bbuy_id = $_SESSION["User_ID"];

    // ตรวจสอบว่ามีค่า ISBN ที่รับมาใน URL หรือไม่
    if(isset($_GET['id'])) {
        $isbn = htmlspecialchars($_GET['id']);

        // เพิ่ม Read_Count ทุกครั้งที่มีการเข้าดูหนังสือ
        $updateReadCountSQL = "UPDATE own SET Read_Count = Read_Count + 1 WHERE ISBN = ? AND BBuy_ID = ?";
        $updateReadCountStmt = $conn->prepare($updateReadCountSQL);
        $updateReadCountStmt->bind_param('ss', $isbn, $bbuy_id);
        $updateReadCountStmt->execute();
        $updateReadCountStmt->close();
    }

    // ดำเนินการต่อไปตามปกติ
    if(mysqli_num_rows($result) > 0) {
        // หากพบหนังสือ
        $book = mysqli_fetch_assoc($result);
        // ดึงข้อมูลเกี่ยวกับหนังสือ
        $title = $book['Book_Name'];
        $author = $book['Auth_Name'];

        // กำหนด URL ของหน้า HTML โดยใช้ชื่อไฟล์เดียวกับ ISBN
        $html_file = '/web/ebook' . '/' . $isbn . '.html';

        // สร้างลิงก์ไปยังหน้าทดลองอ่านหนังสือของหนังสือนี้
        header("location:$html_file");
    } else {
        // หากไม่พบหนังสือ สามารถแสดงข้อความผิดพลาดหรือเรียกกลับไปหน้าหลักได้
    }
} else {
    // ถ้าไม่มี BBuy_ID ใน session ให้ทำการจัดการตามที่คุณต้องการ
    // เช่น กลับไปหน้าหลักหรือทำการล็อกอิน
    header("Location: login.php");
    exit();
}
