<?
include_once("conn.php");
include_once("includes.php");

include_once("templates/HeaderTemplate.php");

$q1 = "select * from dd_catalog, dd_categories where dd_catalog.ItemCategory = dd_categories.CategoryID order by rand() limit 0,1";
$r1 = $pdo->query($q1) or die(mysql_error());



include_once("templates/IndexTemplate.php");
include_once("templates/FooterTemplate.php");

?>