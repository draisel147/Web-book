<?php
session_start();

// Include config file
include 'config.php';

// Define variables and initialize with empty values
$email = $password = '';
$error = '';

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and password are empty
    if (empty(trim($_POST["email"]))) {
        $error = 'Please enter your email or username.';
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $error = 'Please enter your password.';
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($error)) {
        $tables = array(
            'store_manager' => array('id_column' => 'Mana_ID', 'name_column' => 'Mana_Name', 'email_column' => 'Mana_Email', 'password_column' => 'Mana_Password', 'type_column' => 'User_Type'),
            'store_owner'   => array('id_column' => 'Owner_ID', 'name_column' => 'Owner_Name', 'email_column' => 'Owner_Email', 'password_column' => 'Owner_Password', 'type_column' => 'User_Type'),
            'book_buyer'    => array('id_column' => 'BBuy_ID', 'name_column' => 'BBuy_Name', 'email_column' => 'Email', 'password_column' => 'BBuy_Password', 'type_column' => 'User_Type')
        );

        foreach ($tables as $table => $columns) {
            $sql = "SELECT {$columns['id_column']}, {$columns['name_column']}, {$columns['email_column']}, {$columns['password_column']}, {$columns['type_column']} FROM $table WHERE ({$columns['email_column']} = ? OR {$columns['name_column']} = ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $param_email, $param_email);
                $param_email = $email;

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);

                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        mysqli_stmt_bind_result($stmt, $id, $username, $email, $hashed_password, $User_Type);
                        if (mysqli_stmt_fetch($stmt)) {
                            if (password_verify($password, $hashed_password)) {
                                session_start();
                                $_SESSION["User_ID"] = $id;
                                $_SESSION["User_Name"] = $username;
                                $_SESSION["User_Email"] = $email;
                                $_SESSION["User_Type"] = $User_Type;

                                if ($User_Type == 'admin') {
                                    header("location: admin.php");
                                    exit();
                                } else {
                                    header("location: homepage.php");
                                    exit();
                                }
                            } else {
                                $error = 'The password you entered is not valid.';
                            }
                        }
                    }
                } else {
                    $error = "Oops! Something went wrong with {$table} login: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt);
            }
        }

        $error = 'No account found with that email or username.';
        mysqli_close($conn);
    }
}

// Check for errors and display them
if (!empty($error)) {
    echo $error;
}
?>
