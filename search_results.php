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

$ByPage = '10';

$query = array();

if(!empty($_GET[searchterm]))
{
	$query[] = " (ItemName like '%$_GET[searchterm]%' or ItemDesc like '%$_GET[searchterm]%') ";
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



//show the catalog content
$q1 = "select ItemID, ItemName, ItemCategory, ItemSubcategory, ItemPrice from dd_catalog $my_query $order_by limit $Start, $ByPage";
$r1 = $pdo->query($q1) or die(mysql_error());

if($stmt->rowCount($r1) > '0')
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

		$result_rows .= "<tr bgcolor=\"$col\">\n\t<td><a href=\"view_item.php?ItemID=$a1[ItemID]&CategoryID=$a1[ItemCategory]&SubcategoryID=$a1[ItemSubcategory]\" class=BlackLink>$a1[ItemName]</a></td>\n\t\n\t\n</tr>\n\n";
	}
	

	$qnav = "select count(*) from dd_catalog $my_query";
	$rnav = $pdo->query($qnav) or die(mysql_error());
	$anav = mysql_fetch_array($rnav);
	$rows = $anav[0];

	if($rows > $ByPage)
	{
			$NextPrev =  "<br><table align=center width=400><tr>";
			$NextPrev .= "<td align=center class=BlackLink> | ";

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
					$links[] = " <a class=BlackLink href=\"search.php?Start=$PageStart&searchterm=$_GET[searchterm]&CategoryID=$_GET[CategoryID]&SubcategoryID=$_GET[SubcategoryID]&ord1=$_GET[ord1]\">$i2</a>\n\t ";	
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

	