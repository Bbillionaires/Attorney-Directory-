<?
require("conn.php");

$q1 = "DROP TABLE IF EXISTS dd_admin";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "CREATE TABLE dd_admin (
  AdminID int(10) NOT NULL auto_increment,
  username varchar(50) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  PRIMARY KEY (AdminID),
  UNIQUE KEY username(username)) ";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "INSERT INTO dd_admin VALUES (1, 'admin', 'admin', 'neo@cyberia.ca')";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "DROP TABLE IF EXISTS dd_banners";
mysql_query($q1) or die(mysql_error()." at row ".__LINE__);

$q1 = "CREATE TABLE dd_banners (
  BannerID int(10) NOT NULL auto_increment,
  BannerFile text NOT NULL,
  BannerAlt varchar(255) NOT NULL default '',
  BannerURL text NOT NULL,
  PRIMARY KEY (BannerID))";
mysql_query($q1) or die(mysql_error()." at row ".__LINE__);

$q1 = "DROP TABLE IF EXISTS dd_catalog";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "CREATE TABLE dd_catalog (
				ItemID int(10) not null primary key auto_increment,
				ItemName varchar(255) not null unique,
				ItemDesc text not null,
				ItemPrice float(10,2) not null default '0.00',
				ItemImage text not null,
				DownloadURL text not null,
				ItemCategory int(10) not null,
				ItemSubcategory int(10) not null) ";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "DROP TABLE IF EXISTS dd_categories";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "CREATE TABLE dd_categories (
  CategoryID int(10) NOT NULL auto_increment,
  CategoryName varchar(255) NOT NULL default '',
  PRIMARY KEY (CategoryID),
  UNIQUE KEY CategoryName(CategoryName)) ";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");


$q1 = "DROP TABLE IF EXISTS dd_settings";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "CREATE TABLE dd_settings (
  id int(1) NOT NULL default '0',
  SiteName text NOT NULL,
  SiteTitle text NOT NULL,
  SiteKeywords text NOT NULL,
  SiteDesc text NOT NULL,
  help text not null,
  PayPalEmail varchar(255) not null,
  ContactEmail varchar(255) NOT NULL default '',
  currency_id varchar(50) not null default 'USD',
  currency_sign varchar(20) not null default '$',
  download_days int(3) not null default '14',
  sp_payee_email varchar(255) not null,
  sp_vendor_email varchar(255) not null,
  sp_secret_code varchar(255) not null)";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");


$q1 = "DROP TABLE IF EXISTS dd_subcategories";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "CREATE TABLE dd_subcategories (
  SubcategoryID int(10) NOT NULL auto_increment,
  SubcategoryName varchar(255) NOT NULL default '',
  CategoryID int(10) NOT NULL default '0',
  PRIMARY KEY (SubcategoryID)) ";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");



$q1 = "DROP TABLE IF EXISTS dd_orders_content";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "create table dd_orders_content (
					OrderID varchar(32) not null,
					ItemID int(10) not null,
					ItemPrice float(10,2) not null default '0.00',
					ItemQty int(10) not null,
					ItemTotal float(10,2) not null default '0.00',
					download_status char(1) not null default 'n')";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "DROP TABLE IF EXISTS dd_orders_info";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "create table dd_orders_info (
					OrderID int(10) not null primary key auto_increment,
					FirstName varchar(100) not null,
					LastName varchar(100) not null,
					email varchar(255) not null,
					order_paid char(1) not null default 'n',
					order_date int(10) not null,
					order_total float(10,2) not null,
					ExpDate int(10) not null default '0')";
mysql_query($q1) or die(mysql_error()." (script line: ".__LINE__.")");

$q1 = "DROP TABLE IF EXISTS dd_newsletter";
mysql_query($q1) or die(mysql_error()." at line ". __LINE__);	
	
$q1 = "CREATE TABLE dd_newsletter (
	nemail varchar(150) NOT NULL default '',
	PRIMARY KEY (nemail)) ";
mysql_query($q1) or die(mysql_error()." at line ". __LINE__);

$q1 = "DROP TABLE IF EXISTS dd_links";
	mysql_query($q1) or die(mysql_error()." at line ". __LINE__);
	
$q1 = "CREATE TABLE dd_links (
     LinkID int(10) NOT NULL auto_increment,
	 LinkName varchar(255) NOT NULL default '',
	 LinkURL varchar(255) NOT NULL default '',
	 PRIMARY KEY (LinkID)) ";
mysql_query($q1) or die(mysql_error()." at line ". __LINE__);
?>


<html>
<head>
	<title>Database tables installation</title>

	<style>
		body {background-color:white; font-family:verdana; font-size:12; font-weight:bold; color:black}

		a {font-family:verdana; font-size:12; font-weight:bold; color:blue; text-decoration:none}
		a:hover {text-decoration:underline}
	</style>

</head>

<body>

	<center>
	
<p><br>
  <br>
  <br>
  The database tables was installed successfully!</p>
<p>Now login to PHPmyADMIN to add the included forms data.<br>
  <br>
  <font color=red>Delete this file from your server!</font><br>
  <br>
  Click <a href="siteadmin/index.php">here</a> to login to the admin panel.<br>
  Username: admin<br>
  Password: admin<br>
  <br>
</p>
</body>

</html>