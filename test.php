<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        QR Code
                    </div>
                    <div class="card-body text-center">
                        <?php
                            // รับค่าเลขบิลที่ส่งมาจากหน้าอื่นๆ
                            $invoice_number = $_GET['invoice_number'];
                            // สร้างข้อความที่จะนำมาสร้าง QR code
                            $qr_data = "Invoice Number: ".$invoice_number;
                            // สร้าง URL สำหรับ QR code
                            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?data=".urlencode($qr_data);
                            // แสดง QR code ในหน้าเว็บ
                            echo '<img src="'.$qr_url.'" alt="QR Code">';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
