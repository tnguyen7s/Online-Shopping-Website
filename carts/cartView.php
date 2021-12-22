<?php
	require_once("..\checkUserLogin.php");
	if (!hasLogined())
	{
		if (isset($_GET['itemUpdated']))
		{
			header("Location: ..\login.php?action=carts\cartView.php?itemUpdated=true");
		}
		else
		{
			header("Location: ..\login.php?action=carts\cartView.php");
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Cart</title>
		<meta name="description" content="This is a page to display the customer's cart">
		<link rel="stylesheet" href="">
	</head>
	<body style="margin:0px; font-family: Arial,sans-serif;">
		<nav style="background-color:#04AA6D;">
			<ul style="display:flex; list-style-type: none; justify-content: flex-end; margin:0px;">
				<li style="padding:15px 30px;"><a style="color:white;" href="..\index.php">Home</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="..\logout.php?action=index.php">Sign-out</a></li>';
				<li style="padding:15px 30px;"><a style="color:white;" href="..\orders\orders.php">Orders</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="cartView.php">Cart</a></li>
			</ul>
		</nav>

		<main style="display: flex; ">
			<section style="flex-basis: 70%; display: flex; flex-direction: column; margin: 50px 30px;">
				<h2>Shopping Cart</h2>

				<!--Display the cart item-->
					<?php
						require_once("..\db\query.php");
						// get the user current processing order
						$orderId = getProcessingOrderWithCustomerId($_COOKIE['customerId']);
						if ($orderId==null)
						{
							echo '<p>No items in your cart now.</p>';
						}
						else
						{
							// get a list of cart items that are currently in the cart
							$cartItemsList = getCartItemsListWithOrderId(intval($orderId));
							// loop through each cart item and display
							foreach($cartItemsList as $itemId => $itemInfo)
							{
								echo '<div style="display:flex; padding-bottom: 20px; padding-top: 20px; border-bottom: 1px solid #DDD">';

								echo '<div style="flex-basis: 25%;">';
								echo '<img style="padding: 0px 25%;" width=50% src="../img/'.$itemInfo[4].'">';
								echo '</div>';

								echo '<div style="flex-basis: 65%; padding-left: 20
								px; padding-right: 20px;">';
								echo '<h3 style="padding: 10px; margin:0px;font-weight:500;padding-top: 0px; font-size: 1.1em;">'.$itemInfo[0].'</h3>';
								echo '<div style="display: flex; padding: 10px;">';
								echo '<form style="padding-right: 10px;border-right: solid 1px #DDD;" method="POST" action="updateCartItemQuantity.php">';
								echo '<input style="display: none;" name="orderId" value="'.$orderId.'">';
								echo '<input style="display: none;" name="productId" value="'.$itemId.'">';
								echo '<select name="cartItemCount">';
								echo '<option value="'.$itemInfo[2].'">Qty: '.$itemInfo[2].'</option>';
								for ($i=1; $i<=$itemInfo[3]; $i++)
								{
									echo '<option value="'.$i.'">'.$i.'</option>';
								}
								echo '</select>';
								echo '<button style="background: none; color: inherit; border: none; padding: 5px; font: inherit; cursor: pointer; outline: inherit; background-color:#FFD814; border-color: #FCD200; border-radius: 5px; margin: 0px 5px;" type="submit">Update</button>';
								echo '</form>';
								echo '<a href="deleteCartItem.php?productId='.$itemId.'&orderId='.$orderId.'" style="color: #007185; text-decoration-line: none; padding: 5px; padding-left: 10px;">Delete</a>';
								echo '</div>';
								echo '</div>';

								echo '<p style="font-size: 1.2em;font-weight:700; justify-content: flex-end; margin: 0px;">$'.$itemInfo[1].'</p>';

								echo '</div>';
							}

						if (isset($_GET['itemUpdated']) && $_GET['itemUpdated']=='true')
						{
							echo '<p style="color: red;">Cart item has been updated.</p>';
						}
					}
					?>

			</section>

			<section style="flex-basis: 20%; margin: 100px 30px;">
				<!--Display the order's subtotal-->
				<?php
				require_once("..\db\query.php");
				$orderId = getProcessingOrderWithCustomerId($_COOKIE["customerId"]);
				if ($orderId)
				{
					$subtotal = getOrderPriceSubtotal($orderId);

					echo '<div style="border: 1px solid #DDD; padding: 10px; border-radius: 5px; ">';

					echo '<span>Subtotal: </span>';
					echo '<span style="font-size: 1.2em;font-weight:700;">$'.$subtotal.'</span>';
				

					//Ask if user want to ship the order
					echo '<form style="margin-top: 20px;" method="POST" action="..\orders\proceedOrder.php">';
					echo '<input type="checkbox" name="isShipping" value="true">';
					echo '<label for="isShipping">Request shipping for the order.</label><br>';
					echo '<button style="margin-top: 20px; background: none; color: inherit; border: none;padding: 5px 20px;font: inherit; cursor: pointer;outline: inherit;background-color:#FFD814; border-color: #FCD200; border-radius: 8px;" type="submit">
						Proceed
					</button>';
					echo'</form>';

					echo '</div>';
				}
				?>
			</section>
		</main>
	</body>
</html>
