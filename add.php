<?
include_once("conn.php");

//get the product info
$q1 = "select * from dd_catalog where ItemID = '$_GET[ItemID]' ";
$r1 = $pdo->query($q1) or die(mysql_error());
$a1 = mysql_fetch_array($r1);

//add to cart
$q2 = "insert into dd_orders_content set 
							OrderID = '$_COOKIE[PHPSESSID]',
							ItemID = '$a1[ItemID]',
							ItemPrice = '$a1[ItemPrice]',
							ItemQty = '1',
							ItemTotal = '$a1[ItemPrice]' ";
$pdo->query($q2) or die(mysql_error());

//view cart
header("location:view_cart.php");
exit();

?>