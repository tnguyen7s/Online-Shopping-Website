<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Home</title>
		<meta name="description" content="This is a page to allow users to login.">
		<link rel="stylesheet" href="">
	</head>
	<body>
		<main style="border: solid 1px gray; margin: 50px auto; width: 20%; padding: 20px;">
			<h2>Sign-In</h2>

			<?php
			if (isset($_GET['newAccount']) && $_GET['newAccount']=="true")
			{
				echo '<p style="color:red"> Sign-in with your new account.</p>';
			}

			if (isset($_GET["action"]))
			{
				$formAction = 'login.php?action='.$_GET['action'];
			}
			else
			{
				$formAction = 'login.php';
			}

			echo '<form action="'.$formAction.'" method="POST" style="padding: 20px">';

			?>

				<label for="phone" style="margin-bottom:10px;">Mobile phone number</label><br>
				<input name="phone" type="text" style="margin-bottom:10px;"><br>

				<label for="pwd" style="margin-bottom:10px;">Password</label><br>
				<input name="pwd" type="Password" style="margin-bottom:10px;"><br>

				<button type="submit" name="sign-in" value="clicked" style="margin-bottom:10px;">Sign-in</button>
			</form>

<?php
	require_once("db\query.php");
	if (isset($_POST['sign-in']) && $_POST['sign-in']=='clicked')
	{
		// check if the login is valid
		$customerId = isValidLogin($_POST['phone'], $_POST['pwd']);
		if ($customerId!=null)
		{
			// create a session for user in the database
			$sessionId = insertSession(intval($customerId));

			// add sessionId and customerId to cookie (cookie expires after one day => user needs to login again)
			setcookie("sessionId", $sessionId, time()+60*60*24);
			setcookie("customerId", $customerId, time()+60*60*24);


			// once users login successully, redirect back to the page that they are currently at.
			if (isset($_GET['action']))
			{
				header('Location: '.$_GET['action']); 
			}
			else
			{
				header('Location: index.php');
			}
		}
		else
		{
			echo '<p style="color: red;">Invalid phone number or password.</p>';
		}
	}
?>
		</main>

		<nav style="margin: 0px auto; width: 25%; margin-left:38%;">
			<a href="createNewAccount.php" style="padding: 5px 80px;">Create a shopping account</a>
		</nav>
	</body>
</html>