<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Home</title>
		<meta name="description" content="This is a page to allow users to sign-up.">
		<link rel="stylesheet" href="">
	</head>
	<body>
		<main style="border: solid 1px gray; margin: 50px auto; width: 20%; padding: 20px;">
			<h2>Create Account</h2>

			<form action="createNewAccount.php" method="POST" style="padding: 20px;">
				<label for="firstName" style="margin-bottom:10px; font-weight: 700;">First Name</label><br>
				<input name="firstName" type="text" style="margin-bottom:20px;" value=""><br>

				<label for="lastName" style="margin-bottom:10px; font-weight: 700;">Last Name</label><br>
				<input name="lastName" type="text" style="margin-bottom:20px;" value=""><br>

				<label for="phone" style="margin-bottom:10px; font-weight: 700;">Mobile Phone Number</label><br>
				<input name="phone" type="text" style="margin-bottom:20px;" value=""><br>

				<label for="address" style="margin-bottom:10px; font-weight: 700;">Address</label><br>
				<textarea name="address" style="margin-bottom:20px;" value=""></textarea> <br>

				<label for="pwd" style="margin-bottom:10px;font-weight: 700;">Password</label><br>
				<input name="pwd" type="Password" style="margin-bottom:0px;" value=""><br>
				<p style="margin:0px; margin-bottom: 20px;">Password must be at least 6 characters.</p>

				<label for="reenter-pwd" style="margin-bottom:10px;font-weight: 700;">Re-enter password</label><br>
				<input name="reenter-pwd" type="Password" style="margin-bottom:20px;" value=""><br>

				<button type="submit" name="sign-up" value="clicked" style="margin-bottom:20px;">Create your shopping account</button>
			</form>

<?php
	// check missing form input
	require_once("db\query.php");
	if (isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["phone"]) && isset($_POST["address"]) && isset($_POST["pwd"]) && isset($_POST["reenter-pwd"]))
	{
		$missing = array();

		if ($_POST["firstName"]=="")
		{
			$missing[] = "First name";
		}

		if ($_POST["lastName"]=="")
		{
			$missing[] = "Last name";
		}	

		if ($_POST["phone"]=="")
		{
			$missing[] = "Phone number";
		}	

		if ($_POST["address"]=="")
		{
			$missing[] = "Address";
		}	

		if ($_POST["pwd"]=="")
		{
			$missing[] = "Password";
		}	

		if ($_POST["reenter-pwd"]=="")
		{
			$missing[] = "Re-entered password";
		}

		$missingString = "";
		foreach($missing as $m)
		{
			$missingString = $missingString.", ".$m;
		}

		if ($missingString!=""){
			echo '<p style="color:red;">Your form input are missing'.$missingString.'.</p>';
		}

		if ($_POST["pwd"]!=$_POST["reenter-pwd"])
		{
			echo '<p style="color:red;">Password does not match.</p>';
		}

		insertCustomer($_POST["firstName"], $_POST["lastName"], $_POST["phone"], $_POST["address"], $_POST["pwd"]);

		header("Location: login.php?newAccount=true");
	}
?>
		</main>
		</nav>
	</body>
</html>