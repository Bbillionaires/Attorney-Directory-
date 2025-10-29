<?

	include_once("conn.php");
	include_once("includes.php");


	if($_POST[secret_code] == $aset[sp_secret_code] && $_POST[status] == 'SUCCESS')
	{
			include_once("conn.php");
			$q1 = "update dd_orders_info set order_paid = 'y' where OrderID = '$_POST[transaction_ref]'  ";
			$pdo->query($q1) or die(mysql_error());
			
			$q3 = "select email from dd_orders_info where OrderID = '$_POST[transaction_ref]'";
			$r = $pdo->query($q3) or die(mysql_error());
			if($r) { 
				$e = mysql_fetch_row($r);
			}
			
			$q2 = "select ItemID from dd_orders_content where OrderID = '$_POST[transaction_ref]'";
			$result = $pdo->query($q2) or die(mysql_error());
			if($result) {
				while($row = mysql_fetch_row($result)) {
					$q4 = "select ItemName, downloadURL from dd_catalog where ItemID = '$row[0]'";
					$rr = $pdo->query($q4) or die(mysql_error());
					if($rr) {
						$d = mysql_fetch_row($rr);
						$download_links .= "$d[0]: <a href=\"$d[1]\">$d[1]</a><br>";
					}
				}
								
				if($download_links) {
					$q5 = "select ContactEmail from dd_settings";
					$rr = $pdo->query($q5) or die(mysql_error());
					$ce = mysql_fetch_row($rr);
					mail($e[0],"Download links from $_SERVER[HTTP_HOST]!",$download_links,"Content-Type: text/htmlFrom: $ce[0]");
				}
			}
	}
			
?>