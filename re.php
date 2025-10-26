<?
include_once("conn.php");
include_once("includes.php");

if(isset($_POST[s100]) || isset($_POST[s100_x]))
{

	$my_qty = $_POST[qty];

	//recalculate
	while(list($k,$v) = each($_POST[ids]))
	{

		if($my_qty[$k] == '0' || empty($my_qty[$k]))
		{
			$q1 = "delete from dd_orders_content where ItemID = '$v' and OrderID = '$_COOKIE[PHPSESSID]' ";
		}
		else
		{
			$q1 = "update dd_orders_content set 
										ItemQty = '$my_qty[$k]',
										ItemTotal = ItemPrice * '$my_qty[$k]'

										where ItemID = '$v' and OrderID = '$_COOKIE[PHPSESSID]' ";
		}

		$pdo->query($q1) or die(mysql_error());

	}

	header("location:view_cart.php");
	exit();
}
elseif(isset($_POST[s200]) || isset($_POST[s200_x]))
{
	//empty cart
	$q1 = "delete from dd_orders_content where OrderID = '$_COOKIE[PHPSESSID]' ";
	$pdo->query($q1) or die(mysql_error());

	header("location:view_cart.php");
	exit();
}
elseif(isset($_POST[s300]) || isset($_POST[s300_x]))
{
	//continue shopping
	header("location:index.php");
	exit();
}
elseif(isset($_POST[s400]) || isset($_POST[s400_x]))
{
	//checkout
	header("location:checkout.php");
	exit();
}
else
{
	header("location:index.php");
	exit();
}


?>