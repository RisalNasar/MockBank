<?php

	session_start();
	include "database.php";
	include "captcha.php";

	

// define variables and set to empty values
$nameErr = $emailErr = $addressErr = $useridErr = $passwordErr = $depositErr = $captchaErr = $errorMessage = $positiveMessage = "";
$name = $email = $address = $userid = $password1 = $password2 = $deposit = "";


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

    if (empty($_POST["userid"])) 
	{
		$useridErr = "User ID is required";
    }
	else
	{
		$userid = test_input($_POST["userid"]);
		// check if userid is correctly formed
		if(!preg_match("/^[a-zA-Z0-9]*$/", $userid) )
		{
			 $useridErr = "User ID should be alpha numeric and should not contain white spaces";
		}
		// check if userid is unique
		else
		{
			$names = mysqli_query($con, "SELECT account_number FROM accounts WHERE userid='".$userid."'");
			$numOfAccounts=mysqli_num_rows($names);
			if ($numOfAccounts) 
			{
				$useridErr = "User ID is already taken";
			}
			mysqli_free_result($names);
		}
		
	}	
	
	if(empty($_POST["password"]))
	{		
		$passwordErr = "Password is required";
	}
	else
	{
		$password = test_input($_POST["password"]);
		$password2 = test_input($_POST["password2"]);	

		if (strlen($_POST["password"]) < '8') {
			$passwordErr = "Your Password Must Contain At Least 8 Characters!";
		}
		elseif(!preg_match("#[0-9]+#",$password)) {
			$passwordErr = "Your Password Must Contain At Least 1 Number!";
		}
		elseif(!preg_match("#[A-Z]+#",$password)) {
			$passwordErr = "Your Password Must Contain At Least 1 Capital Letter!";
		}
		elseif(!preg_match("#[a-z]+#",$password)) {
			$passwordErr = "Your Password Must Contain At Least 1 Lowercase Letter!";
		}	

		elseif($password != $password2)
		{
			$passwordErr = "Passwords do not match";
		}
		
	}
	
    if (empty($_POST["deposit"])) 
	{
		$depositErr = "Deposit is required";
    }
	else
	{
		$deposit = test_input($_POST["deposit"]);
		$deposit = (float)$deposit;
		// check if deposit is a valid number
		if(!is_numeric($deposit))
		{
			 $depositErr = "Deposit should be valid number";
		}
		else if(!( ($deposit >= 0) && ($deposit <= 100000) ))
		{
			$depositErr = "Deposit should be a number between 0 and 100000";
		}
	}

    if (empty($_POST["captcha"])) 
	{
		$captchaErr = "Captcha value is required";
    }
	else
	{
		$captcha = test_input($_POST["captcha"]);
		if($captcha != $_SESSION['captcha']['code'])
		{
			 $captchaErr = "Captcha value incorrect";
		}
	}


	
	if( ($nameErr . $emailErr . $addressErr . $useridErr . $passwordErr  . $depositErr . $captchaErr ) == "" )
	{
		
		$hash = password_hash($password, PASSWORD_BCRYPT);
		//echo $hash . "<br>";
		
		$sql = "INSERT INTO accounts (name, email, address, userid, password_hash, account_balance)
			VALUES ('" . $name . "', '" . $email . "', '" . $address . "', '" . $userid . "', '" . $hash . "', " . $deposit . ")";
		
		session_start();
		
		
		if (mysqli_query($con, $sql)) {
			$_SESSION['positiveMessage'] = "Record created successfully!  Please login using the set credentials.";
		} else {
			$_SESSION['errorMessage'] = "Error: " . $sql . "<br>" . mysqli_error($con);
		}
		

		mysqli_close($con);
        header('Location: index.php');
        exit;
		
	}
  
  
}

$_SESSION['captcha'] = captcha(); // Create the captcha code and image_src

?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="styles.css">
        <title>Register a New Account</title>
    </head>
    <body>
		<?php include 'logo.php'; ?>
        <h2>Register a New Account</h2>
        <form name="Register" action="register.php" method="POST">
            Name:<input type="text" name="name" value="<?php echo $name ?>" /><span class="errorMessage"> <?php echo $nameErr;?></span><br />
            Email:<input type="text" name="email" value="<?php echo $email ?>" /><span class="errorMessage"> <?php echo $emailErr;?></span><br />
            Address:<input type="text" name="address" value="<?php echo $address ?>" /><span class="errorMessage"> <?php echo $addressErr;?></span><br />
            User ID:<input type="text" name="userid" value="<?php echo $userid ?>" /><span class="errorMessage"> <?php echo $useridErr;?></span><br />
            Password:<input type="password" name="password"  /><span class="errorMessage"> <?php echo $passwordErr;?></span><br />
            Confirm password: <input type="password" name="password2" /><br />
            Initial Deposit:<input type="text" name="deposit" value="<?php echo $deposit ?>" /><span class="errorMessage"> <?php echo $depositErr;?></span><br />
    		Captcha Value:<input type="text" name="captcha" value="" /><span class="errorMessage"> <?php echo $captchaErr;?></span><br />
    		Captcha Image: <?php echo '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code"><br />'; ?>
            <input type="submit" value="Submit" />
            
        </form>
        
        
<br />
<br />
<a href="index.php">Login</a>
<?php include 'footer.php'; ?>
    </body>
</html>
