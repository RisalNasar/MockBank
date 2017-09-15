<?php
	session_start();

	include "logging.php";

	
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 300)) 
	{

		$userid = $_SESSION["USERID"];
		// last request was more than 5 minutes ago
		session_unset();     // unset $_SESSION variable for the run-time 
		session_destroy();   // destroy session data in storage
		session_start();
		$_SESSION['errorMessage'] = "Your session timed out due to inactivity.  Please login again.";

		security_log($userid, $sourceip, "session.php", "INFO", "Session Management", "User session timed out due to inactivity.");

		header('Location: index.php');
	}
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
	
	if (isset($_SESSION['CREATED'])) 
	{
		if(time() - $_SESSION['CREATED'] > 1800) 
		{
			// session started more than 30 minutes ago
			session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
			$_SESSION['CREATED'] = time();  // update creation time
            $_SESSION['TOKEN'] = bin2hex(openssl_random_pseudo_bytes(60) );
		}
	}
	
	$errorMessage = "";

	if( isset($_SESSION["USERID"]) )
	{
		$userid = $_SESSION["USERID"];
		//echo '<pre>'; var_dump($_SESSION); echo '</pre>';
		//echo $userid . "<br />";
        $token = $_SESSION["TOKEN"];

	}
	else
	{
		$_SESSION['errorMessage'] = "You are not logged in.  Please login.";
		header('Location: index.php');
		exit;
	}
	
?>