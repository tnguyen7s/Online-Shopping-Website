<?php
	require_once("..\db\query.php");
	if (isset($_POST["sendmsg"]) && $_POST["sendmsg"]=="clicked")
	{
		insertCommentOnAnItem($_POST['orderId'], $_POST['productId'], $_POST['comment'], date('Y-m-d H:i:s'), $_POST['customerId']);
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Orders</title>
		<meta name="description" content="This is a page for users to leave a message about the order">
		<link rel="stylesheet" href="">
	</head>
	<body style="margin:0px; font-family: Arial,sans-serif;">
		<nav style="background-color:#04AA6D;">
			<ul style="display:flex; list-style-type: none; justify-content: flex-end; margin:0px;">
				<li style="padding:15px 30px;"><a style="color:white;" href="../index.php">Home</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="../logout.php?action=index.php">Sign-out</a></li>;
				<li style="padding:15px 30px;"><a style="color:white;" href="orders.php">Orders</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="../carts/cartView.php">Cart</a></li>
			</ul>
		</nav>

		<h1 style="margin: 100px 10% 30px 10%; font-weight:  600">Leave seller message about your order</h1>

		<?php
			require_once("..\db\query.php");

			// leavesellermsg form goes with the input order id
			$orderId = $_POST['orderId'];

			// get all the cart item in the list
			$items = getCartItemsListWithOrderId($orderId);

			// get order info
			$orderInfo = getOrderInfo($orderId);
			$placedOn = $orderInfo['placedDate'];

			// go through each cart item in the list and display
			foreach ($items as $itemId => $itemInfo) 
			{
				$productInfo = getProductInfo($itemId);
				$productName = $productInfo["productName"];
				$productImage = $productInfo["productImage"];
				$ownerId = $productInfo["ownerId"];

				$sellerName = getSellerInfo($ownerId)["shopName"];

				echo '<div class="itemAndMsg" style="display: flex; flex-direction:column; margin: 30px auto; width: 75%; padding: 50px; border: 1px #D5D9D9 solid;">';

					echo '<div class="item" style="display: flex; padding: 10px; border: 1px #D5D9D9 solid; border-radius: 8px;">';

						echo '<div class="seller" style="display: flex; flex-direction: column; width: 20%">';
						echo '<p style="color:#565959; margin-bottom: 3px;">SELLER</p>';
						echo '<a href="" style="text-decoration-line:none; color: #007185; font-size: 1.2em;">'.$sellerName.'</a>';
						echo '</div>';

						echo '<div class="product" style="display: flex; flex-direction: column; width: 60%">';
						echo '<p style="color:#565959; margin-bottom: 3px;">PRODUCT</p>';
						echo '<div style="display: flex; align-items: center;">';
							echo '<img style="width: 15%; padding: 15px;" src="../img/'.$productImage.'">';
							echo '<a href="..\products\selectProduct.php?productId='.$itemId.'" style="text-decoration-line:none; color: #007185; font-size: 1.1em; font-weight: 600; padding-right: 200px;s">'.$productName.'</a>';
						echo '</div>';
						echo '</div>';


						echo '<div class="order" style="display: flex; flex-direction: column; width: 20%">';
						echo '<p style="color:#565959; margin-bottom: 3px;">ORDER PLACED</p>';
						echo '<p style="margin-bottom: 3px;margin-top: 0px;">'.$placedOn.'</p>';
						echo '<p style="color:#565959; margin-bottom: 3px; margin-top: 0px;">ORDER NUMBER</p>';
						echo '<p style="margin-bottom: 3px; margin-top: 0px;">'.$orderId.'</p>';
						echo '</div>';


					echo '</div>';


					echo '<div class="msgArea" style="display: flex; flex-direction: column; border: 1px #D5D9D9 solid; border-radius: 8px; padding: 20px; margin-top: 30px;">';

						$comments = getCommentsAboutItemInTheOrder($orderId, $itemId);
						foreach ($comments as $commentId => $commentInfo) {
							$commentDateTime = $commentInfo["commentDateTime"];
							$comment = $commentInfo["comment"];

							if ($commentInfo["customerId"]!=0)
							{
								echo '<div style="padding: 20px; border: solid 1px #C7CDD1; margin-bottom: 30px;">';
								$customer = getCustomerInfo($commentInfo["customerId"]);
								$customerName = $customer["firstName"]." ".$customer["lastName"];

								echo '<p style="font-weight: 600;">'.$customerName.'</p>';
								echo '<p style="font-size: 0.9em; color: #2d3b45; margin: 0px;">'.$commentDateTime.'</p>';
								echo '<p style="margin: 20px 0px;">'.$comment.'</p>';
								echo '</div>';
							}
							else
							{
								echo '<div style="padding: 20px; border: solid 2px #067D62; margin-bottom: 30px; border-left-width: 5px;">';
								$seller = getSellerInfo($commentInfo["ownerId"]);
								$shopName = $seller["shopName"];

								echo '<p style="font-weight: 600; color: #067D62;">'.$shopName.'</p>';
								echo '<p style="font-size: 0.9em; color: #2d3b45; margin: 0px;">'.$commentDateTime.'</p>';
								echo '<p style="margin: 20px 0px;">'.$comment.'</p>';
								echo '</div>';
							}
						}

						echo '<form method=POST action="leavesellermsg.php">';
						echo '<input type="text" style="display: none" name="orderId" value='.$orderId.'>';
						echo '<input type="text" style="display: none" name="productId" value='.$itemId.'>';
						echo '<input type="text" style="display: none" name="customerId" value='.$_COOKIE['customerId'].'>';
						echo '<textarea style="width: 90%; padding: 10px;" name="comment"></textarea>';
						echo '<button type="submit" name="sendmsg" value="clicked" style="color: black; background-color:#FFD814; border-color: #FCD200; border-radius: 8px; padding: 10px; text-decoration-line: none; margin-top: 20px;cursor: pointer; border: none;">Send message</button>';
						echo '</form>';
					echo '</div>';

				echo '</div>';
			}


		?>

	</body>
</html>       