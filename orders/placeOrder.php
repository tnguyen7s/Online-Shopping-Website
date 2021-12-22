<?php
	require_once("..\db\query.php");
	$orderId = getProcessingOrderWithCustomerId($_COOKIE["customerId"]);

	require_once('..\app_enums\orderstatusenum.php');
	updateOrderStatus($orderId, OrderStatusEnum::WaitingForSellerResponse);
	updateOrderPlaceDate($orderId, date('Y-m-d'));
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Home</title>
		<meta name="description" content="This is a page to show the order has been placed.">
		<link rel="stylesheet" href="">
	</head>
	<body style="margin:0px; font-family: Arial,sans-serif;">
		<nav style="background-color:#04AA6D;">
			<ul style="display:flex; list-style-type: none; justify-content: flex-end; margin:0px;">
				<li style="padding:15px 30px;"><a style="color:white;" href="../
					index.php">Home</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="../logout.php?action=index.php">Sign-out</a></li>';
				<li style="padding:15px 30px;"><a style="color:white;" href="orders.php">Orders</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="../carts/cartView.php">Cart</a></li>
			</ul>
		</nav>

		<main style="width: 70%; margin: 50px auto; padding: 10px; border: solid 2px #70AD47; border-radius: 5px;">
			<h1 style="color: #2C911B;">Thank you, your order has been placed.</h1>
			<p>Please wait for seller's response.</p>
			<p>Checkout the order page to view more details about your order.</p>
		</main>
	</body>
</html>