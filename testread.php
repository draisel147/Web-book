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
if (isset($_GET['id'])) {
    $isbn = $_GET['id'];

    // เพิ่ม View_Count ทุกครั้งที่มีการเข้าดูหนังสือ
    $updateViewCountSQL = "UPDATE book SET View_Count = View_Count + 1 WHERE ISBN = ?";
    $updateViewCountStmt = $conn->prepare($updateViewCountSQL);
    $updateViewCountStmt->bind_param('s', $isbn);
    $updateViewCountStmt->execute();
    $updateViewCountStmt->close();
}
if(mysqli_num_rows($result) > 0) {
    // หากพบหนังสือ
    $book = mysqli_fetch_assoc($result);
    // ดึงข้อมูลเกี่ยวกับหนังสือ
    $title = $book['Book_Name'];
    $author = $book['Auth_Name'];
    
    // กำหนด URL ของหน้า HTML โดยใช้ชื่อไฟล์เดียวกับ ISBN
    $html_file = '/web/sample' . '/' . $isbn . '.html'; 


    
    // สร้างลิงก์ไปยังหน้าทดลองอ่านหนังสือของหนังสือนี้
    header("location:$html_file");
} else {
    // หากไม่พบหนังสือ สามารถแสดงข้อความผิดพลาดหรือเรียกกลับไปหน้าหลักได้
}
?>
