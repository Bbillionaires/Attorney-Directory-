<?
require_once("conn.php");
require_once("includes.php");
include_once("templates/HeaderTemplate.php");

$q1 = "select * from dd_catalog, dd_categories where dd_catalog.ItemID = '$_GET[ItemID]' and dd_catalog.ItemCategory = dd_categories.CategoryID";
$r1 = $pdo->query($q1) or die(mysql_error());
$a1 = mysql_fetch_array($r1);


include_once("templates/ViewItemTemplate.php");
include_once("templates/FooterTemplate.php");
?>
