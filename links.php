<?php require_once __DIR__ . '/includes.php'; ?>
<?php @include __DIR__ . "/templates/HeaderTemplate.php"; ?>
<?
//get the link
$q1 = "select * from dd_links";
$r1 = $pdo->query($q1) or die(mysql_error());

if($stmt->rowCount($r1) == '0')
{
	echo ("");
	//exit();
}

while($a1 = mysql_fetch_array($r1))
{
?>
<a class=BlackLink href="<?=$a1[LinkURL]?>"><?=$a1[LinkName]?></a><br><BR>
<?
}
?>

<?php @include __DIR__ . "/templates/FooterTemplate.php"; ?>
