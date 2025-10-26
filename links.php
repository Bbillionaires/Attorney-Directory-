<?
//get the link
$q1 = "select * from dd_links";
$r1 = mysql_query($q1) or die(mysql_error());

if(mysql_num_rows($r1) == '0')
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
