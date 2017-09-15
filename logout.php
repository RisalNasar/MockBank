<?php
		include "logging.php";
		session_start();

		$userid = $_SESSION["USERID"];
		if(isset($_SESSION['errorMessage']) )
		{

			$errorMessage = $_SESSION['errorMessage'];
		}
        
        security_log($userid, $sourceip, "logout.php", "INFO", "Logout", "User logged out.");

		session_unset();     // unset $_SESSION variable for the run-time 
		session_destroy();   // destroy session data in storage
		session_start();
		$_SESSION["positiveMessage"] = "You have been successfully logged out.";

		if (isset($errorMessage))
		{

			$_SESSION["errorMessage"] = $errorMessage;
		}
        
		header('Location: index.php');
		
?>