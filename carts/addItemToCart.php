<?php
	require_once("..\checkUserLogin.php");
	require_once("..\db\query.php");

	$login = hasLogined();

	//if user has logined, get the current proccessing order and place the added cart item into the order
	if ($login==true)
	{
		$orderId = getProcessingOrderWithCustomerId(intval($_COOKIE['customerId']));

		if ($orderId==null)
		{
			$orderId = insertOrderWithCustomerId(intval($_COOKIE['customerId']));
		}
		insertCartItem($_POST['productId'], $orderId, $_POST['itemsCount']);
		header('Location: ..\products\selectProduct.php?productId='.$_POST['productId'].'&addedToCart=true');
	}
	// if user has not logined, ask the user to login
	else
	{
		header('Location: ..\login.php?action=products\selectProduct.php?productId='.$_POST['productId']);
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Home</title>
		<meta name="description" content="This is a page to display the user's cart">
		<link rel="stylesheet" href="">
	</head>
	<body>
		<main>

		</main>
	</body>
</html>