<?php
session_start();

// Include config file
include 'config.php';

// Define variables and initialize with empty values
$bbuy_name = $bbuy_mname = $bbuy_lname = $email = $password = $bbuy_address = $gender = $birthdate = $tel = $province = '';
$error = '';

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate bbuy_name
    if (empty(trim($_POST["BBuy_Name"]))) {
        $error = 'Please enter your name.';
    } else {
        $bbuy_name = trim($_POST["BBuy_Name"]);
    }

    // Validate bbuy_mname (Middle Name)
    $bbuy_mname = trim($_POST["BBuy_MName"]);

    // Validate bbuy_lname (Last Name)
    if (empty(trim($_POST["BBuy_LName"]))) {
        $error = 'Please enter your last name.';
    } else {
        $bbuy_lname = trim($_POST["BBuy_LName"]);
    }

    // Validate email
    if (empty(trim($_POST["Email"]))) {
        $error = 'Please enter your email.';
    } else {
        $email = trim($_POST["Email"]);
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $error = 'Please enter a password.';
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $error = 'Password must have at least 6 characters.';
    } else {
        $password = trim($_POST['password']);
    }

    // Validate bbuy_address
    if (empty(trim($_POST["BBuy_Address"]))) {
        $error = 'Please enter your address.';
    } else {
        $bbuy_address = trim($_POST["BBuy_Address"]);
    }

    // Validate gender
    $gender = trim($_POST["GenderName"]);

    // Validate birthdate (optional, adjust as needed)
    $birthdate = empty(trim($_POST["BBuy_BirthDate"])) ? null : trim($_POST["BBuy_BirthDate"]);

    // Validate tel (optional, adjust as needed)
    $tel = empty(trim($_POST["BBuy_Tel"])) ? null : trim($_POST["BBuy_Tel"]);

    // Validate province (optional, adjust as needed)
    $province = empty(trim($_POST["Prov_ID"])) ? null : trim($_POST["Prov_ID"]);

    // Check input errors before inserting into database
    if (empty($error)) {
        // Prepare an insert statement
        $sql = "INSERT INTO book_buyer (BBuy_Name, BBuy_MName, BBuy_LName, Email, BBuy_Password, BBuy_Address, Gender_ID, BBuy_BirthDate, BBuy_Tel, Prov_ID, BBuy_Age) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssisssi", $param_bbuy_name, $param_bbuy_mname, $param_bbuy_lname, $param_email, $param_password, $param_bbuy_address, $param_gender, $param_birthdate, $param_tel, $param_province, $param_age);

            // Set parameters
            $param_bbuy_name = $bbuy_name;
            $param_bbuy_mname = $bbuy_mname;
            $param_bbuy_lname = $bbuy_lname;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_bbuy_address = $bbuy_address;
            $param_gender = $gender;
            $param_birthdate = $birthdate;
            $param_tel = $tel;
            $param_province = $province;

            // Validate age based on birthdate
            $age = calculateAge($birthdate);

            // Set age parameter
            $param_age = $age;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: login.php");
            } else {
                echo 'Oops! Something went wrong. Please try again later.';
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}

// Function to calculate age based on birthdate
function calculateAge($birthdate) {
    if ($birthdate === null) {
        return null;
    }

    $today = new DateTime();
    $birthday = new DateTime($birthdate);
    $age = $today->diff($birthday)->y;
    return $age;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <!-- เพิ่ม Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <br><br>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-info h4 text-center" role="alert">
                    สมัครสมาชิก
                </div>
                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label for="GenderName" class="form-label">เพศ</label>
                        <select class="form-select" name="GenderName" required>
                            <?php
                            $sql = "SELECT * FROM gender ORDER BY Gender_Name";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_array($result)) {
                                echo '<option value="' . $row['Gender_ID'] . '">' . $row['Gender_Name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="Email" class="form-label">Email</label>
                        <input type="email" name="Email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="Password" class="form-label">Password</label>
                        <input type="password" name="password" maxlength="10" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="BBuy_Name" class="form-label">ชื่อ</label>
                        <input type="varchar" name="BBuy_Name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="BBuy_MName" class="form-label">ชื่อกลาง</label>
                        <input type="varchar" name="BBuy_MName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="BBuy_LName" class="form-label">นามสกุล</label>
                        <input type="varchar" name="BBuy_LName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="BBuy_Address" class="form-label">ที่อยู่</label>
                        <textarea name="BBuy_Address" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="BBuy_BirthDate" class="form-label">วันเกิด</label>
                        <input type="date" name="BBuy_BirthDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="BBuy_Tel" class="form-label">เบอร์โทรศัพท์</label>
                        <input type="tel" name="BBuy_Tel" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="Prov_ID" class="form-label">จังหวัด</label>
                        <select class="form-select" name="Prov_ID">
                            <?php
                            $sql_province = "SELECT * FROM province ORDER BY Prov_Name";
                            $result_province = mysqli_query($conn, $sql_province);

                            while ($row_province = mysqli_fetch_array($result_province)) {
                                echo '<option value="' . $row_province['Prov_ID'] . '">' . $row_province['Prov_Name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <input type="submit" name="submit" value="สมัครสมาชิก" class="btn btn-primary">
                    <input type="reset" name="cancel" value="ยกเลิก" class="btn btn-danger"> <br>

                    <a href="login.php">Login</a>
                </form>
            </div>
        </div>
    </div>

    <!-- เพิ่ม Bootstrap JS และ Popper.js (ถ้าใช้ Dropdown) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
