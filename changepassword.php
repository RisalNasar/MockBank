<?php

	include "session.php";
	include "database.php";
	
	// define variables and set to empty values
	$oldPasswordErr = $newPasswordErr = $errorMessage = $positiveMessage = $tokenErr = "";
	$oldPassword = $oldPasswordHash = $newPassword1 = $newPassword2 = "";
	
	$sql = "SELECT password_hash FROM accounts WHERE userid = '" . $userid . "'";
	//echo $sql . "<br>";
	$results = mysqli_query($con, $sql);

	$data = mysqli_fetch_assoc($results);
	//print_r($data);
	$oldPasswordHash = $data['password_hash'];



/* Check if the page was requested from itself via the POST method */
if( $_SERVER["REQUEST_METHOD"] === "POST")
{
       
    if (empty($_POST["oldPassword"])) 
	{
		$oldPasswordErr = "Existing Password is required";
    }
	else
	{
		$oldPassword = test_input($_POST["oldPassword"]);
		if(!password_verify($oldPassword, $oldPasswordHash) )
		{
			 $oldPasswordErr = "Existing Password is incorrect";
			 security_log($userid, $sourceip, "changepassword.php", "SECURITY_WARNING", "Incorrect Password", "Incorrect existing password entered.");
		}
	}
	
	if(empty($_POST["newPassword1"]))
	{		
		$newPasswordErr = "New Password is required";
	}
	else
	{
		$newPassword1 = test_input($_POST["newPassword1"]);
		$newPassword2 = test_input($_POST["newPassword2"]);	

		if (strlen($_POST["newPassword1"]) < '8') {
			$newPasswordErr = "Your Password Must Contain At Least 8 Characters!";
		}
		elseif(!preg_match("#[0-9]+#",$newPassword1)) {
			$newPasswordErr = "Your Password Must Contain At Least 1 Number!";
		}
		elseif(!preg_match("#[A-Z]+#",$newPassword1)) {
			$newPasswordErr = "Your Password Must Contain At Least 1 Capital Letter!";
		}
		elseif(!preg_match("#[a-z]+#",$newPassword1)) {
			$newPasswordErr = "Your Password Must Contain At Least 1 Lowercase Letter!";
		}	

		elseif($newPassword1 != $newPassword2)
		{
			$newPasswordErr = "New Passwords do not match";
		}
		
	}
        
        if(empty($_POST["token"] ))
        {
            $tokenErr = "Tokens were empty in the latest request.  Possible Cross Site Request Forgery attack. Logging out.";
            //$_SESSION['errorMessage'] = $tokenErr;

            security_log($userid, $sourceip, "changepassword.php", "SECURITY_WARNING", "CSRF Token", "Tokens were empty in the latest request. Possible Cross Site Request Forgery attack.");

            header('Location: logout.php');
        }
        else if ($_SESSION["TOKEN"] != $_POST["token"] )
        {
            $tokenErr = "Tokens did not match in the latest request.  Possible Cross Site Request Forgery attack. Logging out.";
            //$_SESSION['errorMessage'] = $tokenErr;

            security_log($userid, $sourceip, "changepassword.php", "SECURITY_WARNING", "CSRF Token", "Tokens did not match in the latest request. Possible Cross Site Request Forgery attack.");

            header('Location: logout.php');
            
        } 
	
	
	if( ($oldPasswordErr . $newPasswordErr . $tokenErr  ) == "" )
	{
	
		$newPasswordHash = password_hash($newPassword1, PASSWORD_BCRYPT);
		
		$sql = "UPDATE accounts SET password_hash='" . $newPasswordHash . "' WHERE userid='" . $userid ."'";

		
		
		if (mysqli_query($con, $sql)) {
			$positiveMessage = "Password successfully updated.";
			security_log($userid, $sourceip, "changepassword.php", "INFO", "Password Updated", "Password successfully updated.");
		} else {
			$errorMessage = "Error: " . $sql . "<br>" . mysqli_error($con);
		}
		
	}
}	
	
	mysqli_free_result($results);
	mysqli_close($con);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="styles.css">

        <title>Change Details</title>
    </head>
	

<body>
<?php include 'logo.php'; ?>

        <form name="ChangePassword" action="changepassword.php" method="POST">
            Old Password:<input type="password" name="oldPassword"  /><span class="errorMessage"> <?php echo $oldPasswordErr;?></span><br />
            New Password:<input type="password" name="newPassword1"  /><span class="errorMessage"> <?php echo $newPasswordErr;?></span><br />
            Confirm New password: <input type="password" name="newPassword2" /><br />
            <input type="hidden" name="token" value="<?php echo $token ?>" />
            <input type="submit" value="Change Password" />
            
        </form>

<br />
<br />
<a href="transact.php">Make A Transaction</a>
<br />
<br />
<a href="account.php">View Account Details</a>
<br />
<br />
<a href="changepassword.php">Change Password</a>
<br />
<br />
<a href="logout.php">Logout</a>
<?php include 'footer.php'; ?>
</body>
</html>
