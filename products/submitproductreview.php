<?php
require_once("..\db\query.php");
if (isset($_POST["submit"]) && $_POST["submit"]=="clicked")
{
	$customerId = $_COOKIE['customerId'];
	$productId = $_POST['productId'];
	$rating = $_POST['rating'];
	$review = $_POST['review'];
	$headline = $_POST['headline'];

	insertProductReview($customerId, $productId, $rating, $review, date('Y-m-d H:i:s'), $headline);
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Healthy shopping - Product Review</title>
		<meta name="description" content="This is a page for users to write a review about the product">
		<link rel="stylesheet" href="">
	</head>
	<body style="margin: 0px
	; font-family: Arial,sans-serif;">
		<nav style="background-color:#04AA6D;">
			<ul style="display:flex; list-style-type: none; justify-content: flex-end; margin:0px;">
				<li style="padding:15px 30px;"><a style="color:white;" href="../index.php">Home</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="../logout.php?action=index.php">Sign-out</a></li>';
				<li style="padding:15px 30px;"><a style="color:white;" href="../orders/orders.php">Orders</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="../carts/cartView.php">Cart</a></li>
			</ul>
		</nav>

		<main>
		<div style="padding: 10px; border: solid 2px #067D62; border-left-width: 15px; width: 60%; margin: 50px auto; border-radius: 8px;">
			<p style="color: #007600; font-weight:600;">Review submitted - Thank you!</p>
			<p>We appreciate your time.</p>

		</div>

		<h1 style="width: 60%; margin: 0px auto; border-bottom: solid 2px #D5D9D9; padding-bottom: 20px; font-weight: 600;">Review your purchases</h1>

		<?php
		require_once("../db/query.php");

		$orders = getCompletedOrders($_COOKIE['customerId']);
		$purchasedItems = array();
		foreach ($orders as $orderId => $orderInfo) {
			$itemsInOrder = getCartItemsListWithOrderId(intval($orderId));
			foreach ($itemsInOrder as $itemId => $itemInfo)
			{
				$purchasedItems[$itemId]["productName"] = $itemInfo[0];
				$purchasedItems[$itemId]["productImage"] = $itemInfo[4];
			}
		}

		echo '<div style="margin: 0px 20%; display: grid; grid-template: repeat(5, 150px)/repeat(3, auto); grid-gap: 20px; align-items: last baseline">';
		foreach ($purchasedItems as $itemId => $itemInfo)
		{
				echo '<div style="display:flex; flex-direction: column;">';
				echo '<img width=16% style="padding: 0px 42%;" src="../img/'.$itemInfo['productImage'].'">';
				echo '<a style="text-decoration-line: none; color: black; text-align: center; padding: 10px;" href="productreview.php?productId='.$itemId.'">'.substr($itemInfo['productName'], 0, 30)."...".'</a>';
				echo '</div>';

		}

		echo '</div>';


		?>

		</main>
	</body>

</html>