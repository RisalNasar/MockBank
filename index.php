<?php
    include "logging.php";

	session_start();
	
	if( isset($_SESSION["USERID"]) )
	{
		header('Location: account.php');
		exit;
	}


	$errorMessage = $positiveMessage = $userid = $password = "";
	$useridErr = $passwordErr = "";


	if( !empty($_SESSION['errorMessage']) )
	{
		$errorMessage = $_SESSION['errorMessage'];
		unset($_SESSION['errorMessage'] );
	}
	if( !empty($_SESSION['positiveMessage']) )
	{
		$positiveMessage = $_SESSION['positiveMessage'];
		unset($_SESSION['positiveMessage'] );
	}
	
	include "database.php";
	
	/* Check if the page was requested from itself via the POST method */
	if( $_SERVER["REQUEST_METHOD"] === "POST")
	{
		   
		if (empty($_POST["userid"])) 
		{
			$useridErr = "User ID is required";
		}
		else
		{
			$userid = test_input($_POST["userid"]);
		}
		
		
		if (empty($_POST["password"])) 
		{
			$passwordErr = "Password is required";
		}
		else
		{
			$password = test_input($_POST["password"]);
		}	
	
	
		if( ($useridErr . $passwordErr ) == "" )
		{
			
			
			$sql = "SELECT password_hash FROM accounts WHERE userid = '" . $userid . "'";
			//echo $sql . "<br>";
			$results = mysqli_query($con, $sql);
			$numOfAccounts=mysqli_num_rows($results);
			
			if ($numOfAccounts == 1) 
			{
				$data = mysqli_fetch_assoc($results);
				//print_r($data);
				$hash = $data['password_hash'];
				if( password_verify($password, $hash) )
				{
					// Create session and authenticate successfully
					session_regenerate_id(true);
					$_SESSION['USERID'] = $userid;
					$_SESSION['CREATED'] = time();
					$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
                    $_SESSION['TOKEN'] = bin2hex(openssl_random_pseudo_bytes(60) );

                    security_log($userid, $sourceip, "index.php", "INFO", "Authentication", "User successfully logged in.");

					header('Location: account.php');
					exit;
				}
			}

			$errorMessage = "Wrong username or password!";
			security_log($userid, $sourceip, "index.php", "SECURITY_WARNING", "Authentication", "User entered wrong username or password. Login unsuccessful.");
			
			mysqli_free_result($results);
			mysqli_close($con);

			
		}
	}

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
        <title>Mock Bank</title>
    </head>
    <body>
	<?php include 'logo.php'; ?>
	


        
            <form name="Login" action="index.php" method="POST">
                <h2>Login</h2>
                Username:<input type="text" name="userid" value="<?php echo $userid ?>" /><span class="errorMessage"> <?php echo $useridErr;?></span><br>
                Password:<input type="password" name="password" value="" /><span class="errorMessage"> <?php echo $passwordErr;?></span><br>
                <input type="submit" value="Login" name="Login" />
            </form>
        
            <br />
             <br>Do not have an account yet? Register as a new user. <a href="register.php">Register</a>
        


</body>
</html>
	<?php include 'footer.php'; ?>
    </body>
</html>
