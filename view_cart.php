<?
include_once("conn.php");
include_once("includes.php");
include_once("templates/HeaderTemplate.php");

//get the cart content
$q1 = "select * from dd_orders_content, dd_catalog where dd_orders_content.OrderID = '$_COOKIE[PHPSESSID]' and dd_orders_content.ItemID = dd_catalog.ItemID order by dd_catalog.ItemName";
$r1 = $pdo->query($q1) or die(mysql_error());

if($stmt->rowCount($r1) == '0')
{
	include_once("templates/EmptyCartTemplate.php");
}
else
{
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

		$rows .= "<tr bgcolor=\"$col\">\t<td class=BlackLink>$a1[ItemName]</td><td align=right>$aset[currency_sign] $a1[ItemPrice]</td>\t\t<td align=center>\t\t<input type=text size=3 name=\"qty[]\" value=\"$a1[ItemQty]\">\t\t<input type=hidden name=\"ids[]\" value=\"$a1[ItemID]\">\t</td>\t<td align=right>$aset[currency_sign] $a1[ItemTotal]</td></tr>";

		$order_total = $order_total + $a1[ItemTotal];
	}

	$order_total = number_format($order_total, 2, ".", ",");

	include_once("templates/ViewCartTemplate.php");

}

include_once("templates/FooterTemplate.php");

?>