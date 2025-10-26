<?
require_once("conn.php");
require_once("includes.php");

if(isset($_POST[smail]))
{
	if(!empty($_POST[nEmail]) && ereg("@", $_POST[nEmail]))
	{
		$q1 = "insert into dd_newsletter set nemail = '$_POST[nEmail]' ";
		mysql_query($q1);

		if(!mysql_error())
		{
			header("location:mail_thankyou.php");
			exit();
		}
		
		else
		{
			header("location:mail_error.php");
			exit();
		}
	}
}

//header("location:mail_thankyou.php.php");
//exit();
?>