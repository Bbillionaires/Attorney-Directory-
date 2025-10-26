<?

require_once("conn.php");
require_once("includes.php");

//get the cart content
$q1 = "select * from dd_orders_content, dd_catalog where dd_orders_content.OrderID = '$_COOKIE[PHPSESSID]' and dd_orders_content.ItemID = dd_catalog.ItemID order by dd_catalog.ItemName";
$r1 = mysql_query($q1) or die(mysql_error());

$col = "white";

while($a1 = mysql_fetch_array($r1))
{
	if($col == "white")
	{
		$col = "dddddd";
	}
	else
	{
		$col = "white";
	}

	$rows .= "<tr bgcolor=\"$col\">\n\t<td class=BlackLink>$a1[ItemName]</td>\n<td align=right>$aset[currency_sign] $a1[ItemPrice]</td>\t\n\t<td align=center>$a1[ItemQty]</td>\n\t<td align=right>$aset[currency_sign] $a1[ItemTotal]</td>\n</tr>\n\n";

	$order_total = $order_total + $a1[ItemTotal];
}

$order_total = number_format($order_total, 2, ".", ",");



if(isset($_POST[submit_out]) || isset($_POST[submit_out_x]))
{
	$my_sFirstName = strip_trim($_POST[sFirstName]);
	$my_sLastName = strip_trim($_POST[sLastName]);
	$my_clientEmail = strip_trim($_POST[clientEmail]);


	if(empty($my_sFirstName))
	{
		$error = "<span class=RedLink>Enter your first name, please!</span>";
	}
	elseif(empty($my_sLastName))
	{
		$error = "<span class=RedLink>Enter your last name, please!</span>";
	}
	elseif(empty($my_clientEmail) || !ereg("@", $my_clientEmail))
	{
		$error = "<span class=RedLink>Enter your email, please!</span>";
	}

	if(empty($error))
	{
		$q1 = "insert into dd_orders_info set 
									FirstName = '$my_sFirstName',
									LastName = '$my_sLastName',
									email = '$my_clientEmail',
									order_date = '$t',
									order_total = '$order_total' ";

		mysql_query($q1) or die(mysql_error());

		$order_id = mysql_insert_id();

		$q2 = "update dd_orders_content set OrderID = '$order_id' where OrderID = '$_COOKIE[PHPSESSID]' ";
		mysql_query($q2) or die(mysql_error());

		//update the mailing list table
		$q3 = "insert into dd_maling_list set 
									names = '$my_sFirstName $my_sLastName',
									email = '$my_clientEmail' ";
		mysql_query($q3);
		
		$payment_option = $_REQUEST[payment_option];
		if($payment_option == 2) {
			//redirect to paypal
			header("location:https://www.paypal.com/xclick?business=$aset[PayPalEmail]&item_name=OrderID $order_id&first_name=$my_sFirstName&last_name=$my_sLastName&email=$my_clientEmail&item_number=1&custom=$order_id&amount=$order_total&currency_code=$aset[currency_id]&notify_url=$site_url/notify.php&return=$site_url/thankyou.php");
			exit();
			
		} else if($payment_option == 1) {
			//redirect to stormpay
			header("location:https://www.stormpay.com/stormpay/handle_gen.php?generic=1&vendor_email=$aset[sp_vendor_email]&payee_email=$aset[sp_payee_email]&transaction_ref=$order_id&product_name=OrderID $order_id&amount=$order_total&require_IPN=1&notify_URL=$site_url/sp_notify.php&return_URL=http://$site_url/thankyou.php");
      			exit();
      		}

	}
}

	require_once("templates/HeaderTemplate.php");
	include_once("templates/CheckoutTemplate.php");

include_once("templates/FooterTemplate.php");


?>