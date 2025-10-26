<?

//enter your MySQL database host name, often it is not necessary to edit this line
$db_host = "localhost";

//enter your MySQL database username
$db_username = "db_username";

//enter your MySQL database password
$db_password = "db_password";

//enter your MySQL database name
$db_name = "db_name";

//Script URL
$site_url = "http://www.mywebsite.com";



/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
//////////////////				DO NOT EDIT BELOW THIS LINE		 //////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

//conect to db
$conn = mysql_connect($db_host, $db_username, $db_password) or die(mysql_error());
$db = mysql_select_db($db_name, $conn) or die(mysql_error());
	
//start session
session_start();

//get the time
$t = time();


?>
