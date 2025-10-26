<?
require_once("conn.php");

$qset = "select * from dd_settings";
$rset = $pdo->query($qset) or die(mysql_error());
$aset = mysql_fetch_array($rset);

function CategorySelect($c) {

	$q1 = "select * from dd_categories order by CategoryName";
	$r1 = $pdo->query($q1) or die(mysql_error());

	if($stmt->rowCount($r1) > '0')
	{
		$SelectCategory = "\n\n<select name=SelectCategory>\n\t<option value=\"\"></option>\n\t";

		while($a1 = mysql_fetch_array($r1))

				if($a1[CategoryID] == $c)
				{
					$SelectCategory .= "<option value=\"$a1[CategoryID]|$a2[SubcategoryID]\" selected>$a1[CategoryName], $a2[SubcategoryName]</option>\n\t";
				}
				else
				{
					$SelectCategory .= "<option value=\"$a1[CategoryID]\">$a1[CategoryName]</option>\n\t";
				}
		}
	

		$SelectCategory .= "</select>\n";
        return $SelectCategory;
	}
	
function CategoryTree($c) {

	$tree = "<table align=center width=\"98%\">\n";

	//get the categories
	$q1 = "select * from dd_categories order by CategoryName ";
	$r1 = $pdo->query($q1) or die(mysql_error());
	while($a1 = mysql_fetch_array($r1))
	{
	$tree .= "<tr>\n\t<td><a class=CategoryName href=\"categories.php?CategoryID=$a1[CategoryID]\"><B>$a1[CategoryName] </B></a></td>\n</tr>\n";
		
		if($c == $a1[CategoryID])
		{
			//get the products
			$q2 = "select * from dd_catalog where ItemCategory = '$a1[CategoryID]'";
			$r2 = $pdo->query($q2) or die(mysql_error());

			while($a2 = mysql_fetch_array($r2))
			{

				//$tree .= "<tr>\n\t<td><a href=\"view_item.php?ItemID=$a2[ItemID]\">&nbsp;$a2[ItemName]</a></td>\n</tr>\n";
			}
		}
	}

	$tree .= "</table>\n";


	return $tree;

}



function show_banners(){

	global $dir;

	$q1 = "select * from dd_banners order by rand() limit 0,1";
	$r1 = $pdo->query($q1) or die(mysql_error());

	if($stmt->rowCount($r1) == '1')
	{
		$a1 = mysql_fetch_array($r1);

		$banners = "<BR><a href=\"$a1[BannerURL]\" target=\"_top\"><img src=\"banners/$a1[BannerFile]\" alt=\"$a1[BannerAlt]\" border=0></a>";
	}

	return $banners;

}

function select_currency($c) {

	$currency_array = array("USD|$", "EUR|&euro;", "GBP|&pound;");

	$select = "<select name=\"currency\">\n\t";

	while(list($k,$v) = each($currency_array))
	{
		$info = explode("|", $v);

		if($c == $info[0])
		{
			$select .= "<option value=\"$v\" selected>$info[0]</option>\n\t";
		}
		else
		{
			$select .= "<option value=\"$v\">$info[0]</option>\n\t";
		}
	}

	$select .= "</select>";

	return $select;

}

function strip_trim($str) {

	$n = strip_tags($str);
	$n = trim($n);

	return $n;
}

function select_days($d) {

	$select = "<select name=\"download_days\">";

	for($i = '1'; $i <= '30'; $i++)
	{
		if($i == $d)
		{
			$select .= "<option value=\"$i\" selected>$i</option>\n\t";
		}
		else
		{
			$select .= "<option value=\"$i\">$i</option>\n\t";
		}
	}

	$select .= "</select>";

	return $select;

}

?>
