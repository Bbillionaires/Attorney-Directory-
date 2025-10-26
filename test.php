<?

//get the categories 
$qi = "select * from re2_categories order by CategoryName";
$ri = $pdo->query($qi) or die(mysql_error());

$Account = "<table width=200>\n";

if($stmt->rowCount($ri) > '0')
{
	while($ai = mysql_fetch_array($ri))
	{
		$Categories .= "<tr>\n\t<td bgcolor=#ECECEC><a class=BlackLinkB href=\"search.php?c=$ai[CategoryID]\">$ai[CategoryName]</a></td>\n</tr>\n";

		//get the subcategories
		$qs = "select * from re2_subcategories where CategoryID = '$ai[CategoryID]' order by SubcategoryName ";
		$rs = $pdo->query($qs) or die(mysql_error());

		if($stmt->rowCount($rs) > '0')
		{
			while($as = mysql_fetch_array($rs))
			{
				$Categories .= "<tr>\n\t<td align=left onmouseover=\"this.style.background='EBEBEB'\" onmouseout=\"this.style.background='white'\"><a class=SubCatLinksB href=\"search.php?c=$ai[CategoryID]&s=$as[SubcategoryID]\">$as[SubcategoryName]</a></td>\n</tr>\n";
			}
		}

	}

	if(empty($_SESSION[AgentID]))
	{
		echo ("");
	}
	else
	{
		if($_SESSION[AccountType] == '1')
		{
			$Account .= "<tr>\n\t<td bgcolor=#D6D5D5><a class=BlackLink href=\"profile.php\">Edit profile</a></td>\n</tr>\n";
		$Account .= "<tr>\n\t<td bgcolor=#D6D5D5><a class=BlackLink href=\"banners.php\">Banners</a></td>\n</tr>\n";
		}
		else
		{
			$Account .= "<tr>\n\t<td bgcolor=#D6D5D5><a class=BlackLink href=\"profile2.php\">Edit profile</a></td>\n</tr>\n";
			
		
		}

		$Account .= "<tr>\n\t<td bgcolor=#D6D5D5><a class=BlackLink href=\"manage.php\">Manage Listings</a></td>\n</tr>\n";		


		//get the number of posted listings
		$qpl = "select count(*) from re2_listings where AgentID = '$_SESSION[AgentID]' ";
		$rpl = $pdo->query($qpl) or die(mysql_error());
		$apl = mysql_fetch_array($rpl);

		$ace = date('d M Y', $_SESSION[AccountExpireDate]);

		$after = ($_SESSION[AccountExpireDate] - $t)/(24*60*60);

		if($after <= '10')
		{
			$RenewAccount = "<br><a class=RedLink href=\"prices2.php\">Renew Account</a><br>";
		}

		$Account .= "<tr>\n\t<td bgcolor=#D6D5D5><a class=BlackLink href=\"logout.php\">Logout</a></td>\n</tr>\n<tr>\n\t<td>Listings: $apl[0]/$_SESSION[MaxOffers]</td>\n</tr>\n<tr>\n\t<td>Expire Date:</td>\n</tr>\n<tr>\n\t<td align=right>$ace</td>\n</tr>\n<tr>\n\t<td align=center>$RenewAccount</td>\n</tr>\n";		
	}

	$Categories .= "</table>\n";

}

?>
