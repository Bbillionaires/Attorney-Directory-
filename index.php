<?
include_once("conn.php");
include_once("includes.php");

include_once(__DIR__ . "/templates/HeaderTemplate.php");

$q1 = "select * from dd_catalog, dd_categories where dd_catalog.catid = dd_categories.catid order by RANDOM() LIMIT 1";
$r1 = $pdo->query($q1) or die(mysql_error());



include_once(__DIR__ . "/templates/IndexTemplate.php");
include_once(__DIR__ . "/templates/FooterTemplate.php");

?>