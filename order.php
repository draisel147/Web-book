<?php
ob_start();
session_start();
include 'config.php';

if (!isset($_SESSION["intLine"])) {
    $_SESSION["intLine"] = 0;
    $_SESSION["ISBN"][0] = $_GET["id"];
    $_SESSION["strQty"][0] = 1;
} else {
    $key = array_search($_GET["id"], $_SESSION["ISBN"]);
    if ((string)$key != "") {
        $_SESSION["strQty"][$key] = $_SESSION["strQty"][$key] + 1;
    } else {
        $_SESSION["intLine"] = $_SESSION["intLine"] + 1;
        $intNewLine = $_SESSION["intLine"];
        $_SESSION["ISBN"][$intNewLine] = $_GET["id"];
        $_SESSION["strQty"][$intNewLine] = 1;
    }
}

// Redirect back to the referring page with a hash identifier
header("location: " . $_SERVER['HTTP_REFERER'] . "#cartUpdated");
exit();
?>
