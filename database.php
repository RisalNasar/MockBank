<?php

/** database connection credentials */
$dbHost="localhost"; //on MySql
$dbUsername="mockbankuser";
$dbPassword="mockmenowandthen1";

// connect to the database
$con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
if (!$con) 
{
	exit('Connect Error (' . mysqli_connect_errno() . ') '
			. mysqli_connect_error());
}
/**set the default client character set */ 
mysqli_set_charset($con, 'utf-8');
/** Check whether a user whose name matches the "userid" field already exists */
mysqli_select_db($con, "mockbank");

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>