<?php require_once __DIR__ . '/includes.php'; ?>
<?php @include __DIR__ . "/templates/HeaderTemplate.php"; ?>
<?php

#########################################################
#    Copyright © EliteWeaver UK All rights reserved.    #
#########################################################
#                                                       #
#  Program         : IPN Development Handler            #
#  Author          : Marcus Cicero                      #
#  File            : notify.php                         #
#  Function        : Skeleton IPN Handler               #
#  Version         : 2.0                                #
#  Last Modified   : 09/20/2003                         #
#  Copyright ©     : EliteWeaver UK                     #
#                                                       #
#########################################################
#    THIS SCRIPT IS FREEWARE AND IS NOT FOR RE-SALE!    #
#########################################################
#              END USER LICENCE AGREEMENT               #
# Redistribution and  use in source and/or binary forms #
# with or without  modification, are permitted provided #
# that the above copyright notice is  reproduced in the #
# script, documentation and/or any other materials that #
# may  have been provided in the original distribution. #
#########################################################
#    Copyright © EliteWeaver UK All rights reserved.    #
#########################################################



// IPN validation modes, choose: 1 or 2

$postmode=1;

           //* 1 = Live Via PayPal Network
           //* 2 = Test Via EliteWeaver UK



// Debugger, 1 = on and 0 = off

$debugger=0;



// Convert super globals on older php builds

	if (phpversion() <= '4.0.6')
	{
		$_SERVER = ($HTTP_SERVER_VARS);
		$_POST = ($HTTP_POST_VARS); }



// No ipn post means this script does not exist

	if (!@$_POST['txn_type'])
	{
		@header("Status: 404 Not Found"); exit; }

	else
	{
		@header("Status: 200 OK");  // Prevents ipn reposts on some servers



// Add "cmd" to prepare for post back validation
// Read the ipn post from paypal or eliteweaver uk
// Fix issue with php magic quotes enabled on gpc
// Apply variable antidote (replaces array filter)
// Destroy the original ipn post (security reason)
// Reconstruct the ipn string ready for the post


		$postipn = 'cmd=_notify-validate'; // Notify validate

	foreach ($_POST as $ipnkey => $ipnval)
	{
	if (get_magic_quotes_gpc())
		$ipnval == stripslashes ($ipnval); // Fix issue with magic quotes
	if (!eregi("^[_0-9a-z-]{1,30}$",$ipnkey)
	|| !strcasecmp ($ipnkey, 'cmd'))
	{ // ^ Antidote to potential variable injection and poisoning
	unset ($ipnkey); unset ($ipnval); } // Eliminate the above
	if (@$ipnkey != '') { // Remove empty keys (not values)
		@$_PAYPAL[$ipnkey] = $ipnval; // Assign data to new global array
	unset ($_POST); // Destroy the original ipn post array, sniff...

		$postipn.='&'.@$ipnkey.'='.urlencode(@$ipnval); }} // Notify string
		$error=0; // No errors let's hope it's going to stays like this!



// IPN validation mode 1: Live Via PayPal Network

	if ($postmode == 1)
	{
		$domain = "www.paypal.com";
	}

// IPN validation mode 2: Test Via EliteWeaver UK

	elseif ($postmode == 2)
	{
		$domain = "www.eliteweaver.co.uk"; }

// IPN validation mode was not set to 1 or 2

	else
	{
		$error=1;
		$bmode=1;
	if ($debugger) debugInfo(); }


@set_time_limit(60); // Attempt to double default time limit incase we switch to Get



// Post back the reconstructed instant payment notification

		$socket = @fsockopen($domain,80,$errno,$errstr,30);
		$header = "POST /cgi-bin/webscr HTTP/1.0\r";
		$header.= "User-Agent: PHP/".phpversion()."\r";
		$header.= "Referer: ".$_SERVER['HTTP_HOST'].
		$_SERVER['PHP_SELF'].@$_SERVER['QUERY_STRING']."\r";
		$header.= "Server: ".$_SERVER['SERVER_SOFTWARE']."\r";
		$header.= "Host: ".$domain.":80\r";
		$header.= "Content-Type: application/x-www-form-urlencoded\r";
		$header.= "Content-Length: ".strlen($postipn)."\r";
		$header.= "Accept: */*\r\r";

//* Note: "Connection: Close" is not required using HTTP/1.0



// Problem: Now is this your firewall or your ports?

            if (!$socket && !$error)
            {

// Switch to a Get request for a last ditch attempt!

		$getrq=1;

	if (phpversion() >= '4.3.0'
	&& function_exists('file_get_contents'))
	{} // Checking for a new function
	else
	{ // No? We'll create it instead

function file_get_contents($ipnget) {
		$ipnget = @file($ipnget);
	return $ipnget[0];
		}}

                   $response = @file_get_contents('http://'.$domain.':80/cgi-bin/webscr?'.$postipn);

	if (!$response)
	{
		$error=1;
		$getrq=0;

	if ($debugger) debugInfo();
	// If this is as far as you get then you need a new web host!
			}}



// If no problems have occured then we proceed with the processing

	else
	{
		@fputs ($socket,$header.$postipn."\r\r"); // Required on some environments
	while (!feof($socket))
	{
		$response = fgets ($socket,1024); }}
		$response = trim ($response); // Also required on some environments



// uncomment '#' to assign posted variables to local variables
#extract($_PAYPAL); // if globals is on they are already local

// and/or >>>

// refer to each ipn variable by reference (recommended)
// $_PAYPAL['receiver_email']; etc... (see: ipnvars.txt)



// IPN was confirmed as both genuine and VERIFIED

	if (!strcmp ($response, "VERIFIED"))
	{
		if(variableAudit('payment_status','Completed'))
		{
			$q1 = "update dd_orders_info set order_paid = 'y' where OrderID = '$custom'  ";
			$pdo->query($q1) or die(mysql_error());
			
			$q3 = "select email from dd_orders_info where OrderID = '$custom'";
			$r = $pdo->query($q3) or die(mysql_error());
			if($r) { 
				$e = mysql_fetch_row($r);
			}
						
			$q2 = "select ItemID from dd_orders_content where OrderID = '$custom'";
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
// Check that the "payment_status" variable is: Completed
// If it is Pending you may want to inform your customer?
// Check your db to ensure this "txn_id" is not a duplicate
// You may want to check "payment_gross" or "mc_gross" matches listed prices?
// You definately want to check the "receiver_email" or "business" is yours
// Update your db and process this payment accordingly

//***************************************************************//
//* Tip: Use the internal auditing function to do some of this! *//
//* **************************************************************************************//
//* Help: if(variableAudit('mc_gross','0.01') &&					 *//
//* 	     variableAudit('receiver_email','paypal@domain.com') && 			 *//
//* 	     variableAudit('payment_status','Completed')){ $do_this; } else { do_that; } *//
//****************************************************************************************//
			}



// IPN was not validated as genuine and is INVALID

	elseif (!strcmp ($response, "INVALID"))
	{

// Check your code for any post back validation problems
// Investigate the fact that this could be a spoofed IPN
// If updating your db, ensure this "txn_id" is not a duplicate
			}



	else
	{ // Just incase something serious should happen!
			}}

	if ($debugger) debugInfo();



#########################################################
#     Inernal Functions : variableAudit & debugInfo     #
#########################################################



// Function: variableAudit
// Easy LOCAL to IPN variable comparison 
// Returns 1 for match or 0 for mismatch

function variableAudit($v,$c)
{
	global  $_PAYPAL;
    if (!strcasecmp($_PAYPAL[$v],$c)) 
    { return 1; } else { return 0; } 
} 



// Function: debugInfo
// Displays debug info 
// Set $debugger to 1

function debugInfo()
{
	global  $_PAYPAL,
		$postmode,
		$socket,
		$error,
		$postipn,
		$getrq,
		$response;

		$ipnc = strlen($postipn)-21;
		$ipnv = count($_PAYPAL)+1;

	@flush();
	@header('Cache-control: private'."\r");
	@header('Content-Type: text/plain'."\r");
	@header('Content-Disposition: inline; filename=debug.txt'."\r");
	@header('Content-transfer-encoding: ascii'."\r");
	@header('Pragma: no-cache'."\r");
	@header('Expires: 0'."\r\r");
	echo '#########################################################'."\r";
	echo '#    Copyright © EliteWeaver UK All rights reserved.    #'."\r";
	echo '#########################################################'."\r";
	echo '#              END USER LICENCE AGREEMENT               #'."\r";
	echo '# Redistribution and  use in source and/or binary forms #'."\r";
	echo '# with or without  modification, are permitted provided #'."\r";
	echo '# that the above copyright notice is  reproduced in the #'."\r";
	echo '# script, documentation and/or any other materials that #'."\r";
	echo '# may  have been provided in the original distribution. #'."\r";
	echo '#########################################################'."\r";
	echo '# <-- PayPal IPN Variable Output & Status Debugger! --> #'."\r";
	echo '#########################################################'."\r\r";
	if (phpversion() >= '4.3.0' && $socket)
	{
	echo 'Socket Status: '."\r\r";
	print_r (socket_get_status($socket));
	echo "\r\r"; }
	echo 'PayPal IPN: '."\r\r";
	print_r($_PAYPAL);
	echo "\r\r".'Validation String: '."\r\r".wordwrap($postipn, 64, "\r", 1);
	echo "\r\r\r".'Validation Info: '."\r";
	echo "\r\t".'PayPal IPN String Length Incoming => '.$ipnc."\r";
	echo "\t".'PayPal IPN String Length Outgoing => '.strlen($postipn)."\r";
	echo "\t".'PayPal IPN Variable Count Incoming => ';
	print_r(count($_PAYPAL));
	echo "\r\t".'PayPal IPN Variable Count Outgoing => '.$ipnv."\r";
	if ($postmode == 1)
	{
	echo "\r\t".'IPN Validation Mode => Live -> PayPal, Inc.'; }
	elseif ($postmode == 2)
	{
	echo "\r\t".'IPN Validation Mode => Test -> EliteWeaver.'; }
	else
	{
	echo "\r\t".'IPN Validation Mode => Incorrect Mode Set!'; }
	echo "\r\r\t\t".'IPN Validate Response => '.$response;
	if (!$getrq && !$error)
	{
	echo "\r\t\t".'IPN Validate Method => POST (success)'."\r\r"; }
	elseif ($getrq && !$error)
	{
	echo "\r\t\t".'IPN Validate Method => GET (success)'."\r\r"; }
	elseif ($bmode)
	{
	echo "\r\t\t".'IPN Validate Method => NONE (stupid)'."\r\r"; }
	elseif ($error)
	{
	echo "\r\t\t".'IPN Validate Method => BOTH (failed)'."\r\r"; }
	else
	{
	echo "\r\t\t".'IPN Validate Method => BOTH (unknown)'."\r\r"; }
	echo '#########################################################'."\r";
	echo '#    THIS SCRIPT IS FREEWARE AND IS NOT FOR RE-SALE!    #'."\r";
	echo '#########################################################'."\r\r";
	@flush();

}


// Terminate the socket connection (if open) and exit

	@fclose ($socket); exit;


#########################################################
#    Copyright © EliteWeaver UK All rights reserved.    #
#########################################################
#              END USER LICENCE AGREEMENT               #
# Redistribution and  use in source and/or binary forms #
# with or without  modification, are permitted provided #
# that the above copyright notice is  reproduced in the #
# script, documentation and/or any other materials that #
# may  have been provided in the original distribution. #
#########################################################
#    THIS SCRIPT IS FREEWARE AND IS NOT FOR RE-SALE!    #
#########################################################

?>

<?php @include __DIR__ . "/templates/FooterTemplate.php"; ?>
