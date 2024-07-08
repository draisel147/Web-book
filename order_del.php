<?php
ob_start();
session_start();
include 'condb.php';

if(!isset($_SESSION["intLine"]))    //เช็คว่าแถวเป็นค่าว่างมั๊ย ถ้าว่างให้ทำงานใน {}
{
	 $_SESSION["intLine"] = 0;
	 $_SESSION["ISBN"][0] = $_GET["id"];   //รหัสสินค้า
	 $_SESSION["strQty"][0] = 1;                   //จำนวนสินค้า
	 header("location:cart.php");
}
else
{
	
	$key = array_search($_GET["id"], $_SESSION["ISBN"]);
	if((string)$key != "")
	{
		 $_SESSION["strQty"][$key] = $_SESSION["strQty"][$key] - 1;
	}
	else
	{
		 $_SESSION["intLine"] = $_SESSION["intLine"] + 1;
		 $intNewLine = $_SESSION["intLine"];
		 $_SESSION["ISBN"][$intNewLine] = $_GET["Cart_ID"];
		 $_SESSION["strQty"][$intNewLine] = 1;
	}
	 header("location:cart.php");
}
?>