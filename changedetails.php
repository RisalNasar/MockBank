<?php

	include "session.php";
	include "database.php";
	
	// define variables and set to empty values
	$nameErr = $emailErr = $addressErr = $useridErr = $errorMessage = $tokenErr = $positiveMessage = "";
	$name = $email = $address = "";
	
	$sql = "SELECT name, email, address, account_number FROM accounts WHERE userid = '" . $userid . "'";
	//echo $sql . "<br>";
	$results = mysqli_query($con, $sql);

	$data = mysqli_fetch_assoc($results);
	//print_r($data);
	$name = $data['name'];
	$email = $data['email'];
	$address = $data['address'];
	$account_number = $data['account_number'];
	$newuserid = $userid;



/* Check if the page was requested from itself via the POST method */
if( $_SERVER["REQUEST_METHOD"] === "POST")
{
       
    if (empty($_POST["name"])) 
	{
		$nameErr = "Name is required";
    }
	else
	{
		$name = test_input($_POST["name"]);
		// check if name only contains letters and whitespace
		if(!preg_match("/^[a-zA-Z ]*$/", $name) )
		{
			 $nameErr = "Only letters and white space allowed";
		}
	}
	
	
    if (empty($_POST["email"])) 
	{
		$emailErr = "Email is required";
    }
	else
	{
		$email = test_input($_POST["email"]);
		// check if e-mail address is well-formed
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			 $emailErr = "Invalid email format";
		}
	}
	
    if (empty($_POST["address"])) 
	{
		$addressErr = "Address is required";
    }
	else
	{
		$address = test_input($_POST["address"]);
	}

    if (empty($_POST["newuserid"])) 
	{
		$useridErr = "User ID is required";
    }
	else
	{
		$newuserid = test_input($_POST["newuserid"]);
		// check if newuserid is correctly formed
		if(!preg_match("/^[a-zA-Z0-9]*$/", $newuserid) )
		{
			 $useridErr = "User ID should be alpha numeric and should not contain white spaces";
		}
		// check if newuserid is unique
		else if ($newuserid != $_SESSION['USERID'])
		{
			$names = mysqli_query($con, "SELECT account_number FROM accounts WHERE userid='".$newuserid."'");
			$numOfAccounts=mysqli_num_rows($names);
			if ($numOfAccounts ) 
			{
				$useridErr = "User ID is already taken";
			}
			mysqli_free_result($names);
		}
		
	}	
        

        if(empty($_POST["token"] ))
        {
            $tokenErr = "Tokens were empty in the latest request.  Possible Cross Site Request Forgery attack. Logging out.";
            //$_SESSION['errorMessage'] = $tokenErr;

            security_log($userid, $sourceip, "changedetails.php", "SECURITY_WARNING", "CSRF Token", "Tokens were empty in the latest request. Possible Cross Site Request Forgery attack.");

            header('Location: logout.php');
        }
        else if ($_SESSION["TOKEN"] != $_POST["token"] )
        {
            $tokenErr = "Tokens did not match in the latest request.  Possible Cross Site Request Forgery attack. Logging out.";
            //$_SESSION['errorMessage'] = $tokenErr;

            security_log($userid, $sourceip, "changedetails.php", "SECURITY_WARNING", "CSRF Token", "Tokens did not match in the latest request. Possible Cross Site Request Forgery attack.");

            header('Location: logout.php');
            
        } 
	
	
	if( ($nameErr . $emailErr . $addressErr . $useridErr . $tokenErr ) == "" )
	{
		
		
		$sql = "UPDATE accounts SET name='" . $name . "', email='" . $email . "', 
		address='" . $address . "', userid='" . $newuserid . "' WHERE account_number=" . $account_number;

		
		
		if (mysqli_query($con, $sql)) {
			$positiveMessage = "Data successfully updated.";

			changedetails_log($userid, $sourceip, "changedetails.php", "INFO", "Change In Details", $name, $email, $address, $newuserid);

			if($userid != $newuserid )
			{
				$event = "User ID changed from " . $userid . " to " . $newuserid;
				security_log($userid, $sourceip, "changedetails.php", "SECURITY_WARNING", "UserID Changed", $event);

			}

			$_SESSION['USERID'] = $newuserid;
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

        <form name="ChangeDetails" action="changedetails.php" method="POST">
            Name:<input type="text" name="name" value="<?php echo $name ?>" /><span class="errorMessage"> <?php echo $nameErr;?></span><br />
            Email:<input type="text" name="email" value="<?php echo $email ?>" /><span class="errorMessage"> <?php echo $emailErr;?></span><br />
            Address:<input type="text" name="address" value="<?php echo $address ?>" /><span class="errorMessage"> <?php echo $addressErr;?></span><br />
            User ID:<input type="text" name="newuserid" value="<?php echo $newuserid ?>" /><span class="errorMessage"> <?php echo $useridErr;?></span><br />
            <input type="hidden" name="token" value="<?php echo $token ?>" />
            <input type="submit" value="Change Details" />
            
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
