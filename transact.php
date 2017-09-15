<?php

	include "session.php";
	include "database.php";
	
	// define variables and set to empty values
	$recepientAccountNumberErr = $amountErr = $errorMessage = $positiveMessage = $tokenErr = "";
	$recepientAccountNumber = $amount = "";

	$sql = "SELECT account_balance FROM accounts WHERE userid = '" . $userid . "'";
	//echo $sql . "<br>";
	$results = mysqli_query($con, $sql);

	$data = mysqli_fetch_assoc($results);
	//print_r($data);
	$account_balance = $data['account_balance'];

/* Check if the page was requested from itself via the POST method */
if( $_SERVER["REQUEST_METHOD"] === "POST")
{
       
    if (empty($_POST["recepientAccountNumber"])) 
	{
		$recepientAccountNumberErr = "Recepient Account Number is required";
    }
	else
	{
		$recepientAccountNumber = test_input($_POST["recepientAccountNumber"]);
		// check if account number only contains numbers
		if(!preg_match("/^[0-9]*$/", $recepientAccountNumber) )
		{
			 $recepientAccountNumberErr = "Account Number should be a number";
		}
		else
		{
			$names = mysqli_query($con, "SELECT account_number FROM accounts WHERE account_number=".$recepientAccountNumber);
			$numOfAccounts=mysqli_num_rows($names);
			if ($numOfAccounts != 1 ) 
			{
				$recepientAccountNumberErr = "Recepient Account Number is not valid";
			}
			mysqli_free_result($names);			
		}
	}
	
	
    if (empty($_POST["amount"])) 
	{
		$amountErr = "Amount is required";
    }
	else
	{
		$amount = test_input($_POST["amount"]);
		$amount = (float)$amount;
		// check if amount is a valid number
		if(!is_numeric($amount))
		{
			 $amountErr = "Amount should be valid number";
		}
		else if(!( ($amount > 0) && ($amount <= $account_balance) ))
		{
			$amountErr = "Amount should be a number between 0 and the current balance " . $account_balance;
		}
	}
        
        if(empty($_POST["token"] ))
        {
            $tokenErr = "Tokens were empty in the latest request.  Possible Cross Site Request Forgery attack. Logging out.";
            //$_SESSION['errorMessage'] = $tokenErr;

            security_log($userid, $sourceip, "transact.php", "SECURITY_WARNING", "CSRF Token", "Tokens were empty in the latest request. Possible Cross Site Request Forgery attack.");

            header('Location: logout.php');
        }
        else if ($_SESSION["TOKEN"] != $_POST["token"] )
        {
            $tokenErr = "Tokens did not match in the latest request.  Possible Cross Site Request Forgery attack. Logging out.";
            //$_SESSION['errorMessage'] = $tokenErr;

            security_log($userid, $sourceip, "transact.php", "SECURITY_WARNING", "CSRF Token", "Tokens did not match in the latest request. Possible Cross Site Request Forgery attack.");

            header('Location: logout.php');
            
        }      
	
	
	if( ($recepientAccountNumberErr . $amountErr . $tokenErr  ) == "" )
	{
		mysqli_autocommit($con, false);
		$flag = true;
		
		$sql1 = "UPDATE accounts SET account_balance= account_balance - " . $amount . " WHERE userid='" . $userid . "'";
		$sql2 = "UPDATE accounts SET account_balance= account_balance + " . $amount . " WHERE account_number=" .
			$recepientAccountNumber;
			
		
		if (!mysqli_query($con, $sql1)) 
		{
			$errorMessage = "Error: " . $sql1 . "<br>" . mysqli_error($con);
			$flag = false;
		}
		
		if (!mysqli_query($con, $sql2)) 
		{
			$errorMessage = "Error: " . $sql2 . "<br>" . mysqli_error($con);
			$flag = false;
		}
		
		if($flag)
		{
			mysqli_commit($con);
			$_SESSION['positiveMessage'] = "An amount of " . $amount . " was sent to account number " . 
				$recepientAccountNumber . " successfully!";
			mysqli_autocommit($con, true);

			transaction_log($userid, $sourceip, "transact.php", "INFO", "Transaction", $recepientAccountNumber, $amount);


			header('Location: account.php');
			exit;
			
		}
		
		mysqli_autocommit($con, true);
		

		
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


        <form name="Make A Transaction" action="transact.php" method="POST">
            Recepient Account Number:<input type="text" name="recepientAccountNumber" value="<?php echo $recepientAccountNumber ?>" /><span class="errorMessage"> <?php echo $recepientAccountNumberErr;?></span><br />
            Amount:<input type="text" name="amount" value="<?php echo $amount ?>" /><span class="errorMessage"> <?php echo $amountErr;?></span><br />
            <input type="hidden" name="token" value="<?php echo $token ?>" />
            <input type="submit" value="Send Amout" />
            
        </form>

<br />
<br />
<a href="account.php">View Account Details</a>
<br />
<br />
<a href="logout.php">Logout</a>
<?php include 'footer.php'; ?>
</body>
</html>
