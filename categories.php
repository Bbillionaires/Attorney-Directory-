<?
include_once("conn.php");
include_once("includes.php");

include_once("templates/HeaderTemplate.php");


if(empty($_GET[Start]))
{
	$Start = '0';
}
else
{
	$Start = $_GET[Start];
}

$ByPage = '20';

$query = array();

if(!empty($_GET[what]))
{
	$query[] = " (ItemName like '%$_GET[what]%' or ItemDesc like '%$_GET[what]%') ";
}

if(!empty($_GET[CategoryID]))
{
	$query[] = " ItemCategory = '$_GET[CategoryID]' ";
}

if(!empty($_GET[SubcategoryID]))
{
	$query[] = " ItemSubcategory = '$_GET[SubcategoryID]' ";
}

if(!empty($query))
{
	$my_query = implode(" and ", $query);

	$my_query = " where $my_query";
}

if(!empty($_GET[ord1]))
{
	$order_by = " order by $_GET[ord1] ";
}
else
{
	$order_by = " order by ItemName ";
}

//show the catalog content
$q1 = "select ItemID, ItemName, ItemCategory ItemPrice from dd_catalog $my_query $order_by limit $Start, $ByPage";
$r1 = mysql_query($q1) or die(mysql_error());

if(mysql_num_rows($r1) > '0')
{
	
	$col = "#D7D7D7";

	while($a1 = mysql_fetch_array($r1))
	{
		if($col == "#D7D7D7")
		{
			$col = "#E7E5E5";
		}
		else
		{
			$col = "#D7D7D7";
		}

		$result_rows .= "<tr bgcolor=\"$col\">\n\t<td cellpadding=4><a href=\"view_item.php?ItemID=$a1[ItemID]\" class=BlackLink>$a1[ItemName]</a></td>\n\t\n</tr>\n\n";
	}
	

	$qnav = "select count(*) from dd_catalog $my_query";
	$rnav = mysql_query($qnav) or die(mysql_error());
	$anav = mysql_fetch_array($rnav);
	$rows = $anav[0];

	if($rows > $ByPage)
	{
			$NextPrev =  "<br><table align=center width=400><tr>";
			$NextPrev .= "<td align=center class=BlackLink  cellpadding=4> | ";

			$pages = ceil($rows/$ByPage);

			for($i = 0; $i <= ($pages); $i++)
			{
				$PageStart = $ByPage*$i;

				$i2 = $i + 1;

				if($PageStart == $Start)
				{
					$links[] = " <span class=RedLink>$i2</span>\n\t ";
				}
				elseif($PageStart < $rows)
				{
					$links[] = " <a class=BlackLink href=\"search.php?Start=$PageStart&what=$_GET[what]&CategoryID=$_GET[CategoryID]&ord1=$_GET[ord1]\">$i2</a>\n\t ";	
				}
			}

			$links2 = implode(" | ", $links);
		
			$NextPrev .= $links2;

			$NextPrev .=  "| </td>";

			$NextPrev .= "</tr></table><br>\n";

	}

	include_once("templates/ResultsTemplate.php");
}
else
{
	include_once("templates/NoResultsTemplate.php");
}


include_once("templates/FooterTemplate.php");

?>

	