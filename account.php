<?php

	include "session.php";


	include "database.php";
	
	$errorMessage = $positiveMessage = "";
	
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
	
	$sql = "SELECT account_number, name, email, address, account_balance FROM accounts WHERE userid = '" . $userid . "'";
	//echo $sql . "<br>";
	$results = mysqli_query($con, $sql);

	$data = mysqli_fetch_assoc($results);
	//print_r($data);
	$account_number = $data['account_number'];
	$name = $data['name'];
	$email = $data['email'];
	$address = $data['address'];
	$account_balance = $data['account_balance'];

	mysqli_free_result($results);
	mysqli_close($con);

?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles.css">
<style>
table {
    width:50%;
	
}
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;

}
</style>
</head>
<body>
<?php include 'logo.php'; ?>
<h3>Welcome <?php echo $name . "!"; ?></h3><br />
<h4>Your Account Details:</h4>
<table>
  <tr>
    <th>Name</th>
    <td><?php echo $name; ?></td>
  </tr>
   <tr>
    <th>User ID</th>
    <td><?php echo $userid; ?></td>
  </tr>
  <tr>
    <th>Account Number</th>
    <td><?php echo $account_number; ?></td>
  </tr>
  <tr>
    <th>Email</th>
    <td><?php echo $email; ?></td>
  </tr>
  <tr>
    <th>Address</th>
    <td><?php echo $address; ?></td>
  </tr>
  <tr>
    <th>Account Balance</th>
    <td><?php echo $account_balance; ?></td>
  </tr>
 
</table>

<br />
<br />
<a href="transact.php">Make A Transaction</a>
<br />
<br />
<a href="changedetails.php">Change Details</a>
<br />
<br />
<a href="changepassword.php">Change Password</a>
<br />
<br />
<a href="logout.php">Logout</a>
<?php include 'footer.php'; ?>
</body>
</html>



