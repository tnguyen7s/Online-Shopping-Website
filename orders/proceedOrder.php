<?php
	// update the order's information
	require_once("..\db\query.php");

	// Get the user's current processing order Id
	$orderId = getProcessingOrderWithCustomerId($_COOKIE["customerId"]);
	
	if (isset($_POST['isShipping']))
	{	
		insertShipmentForOrder($_COOKIE["customerId"], $orderId);
	}
	// else if (!isset($_GET['updateShippingAddress']) |\ !isset($_GET['addShippingInstruction']))
	// {
	// 	updateOrderTransportationMethod($orderId, 1, 0);
	// }
?>

<!DOCTYPE html>
<html lang='en'>
	<head>
		<title>Healthy shopping - Checkout</title>
		<meta name="description" content="This is a page to display the checkout">
		<link rel="stylesheet" href="">
	</head>

	<body style="font-family: Arial,sans-serif;">
		<h1 style="margin: 20px auto; text-align: center; font-weight:550">Checkout</h1>
		<main style="display: flex; margin: 0px 200px;">
			<section style="flex-basis: 60%; display:flex; flex-direction: column;">
				<div id="shoppingAddress" style="display: flex; justify-content: space-between; ">
					<?php
						require_once("..\db\query.php");
						$orderId = getProcessingOrderWithCustomerId($_COOKIE['customerId']);

						$orderInfo = getOrderInfo($orderId);
						if ($orderInfo["isPickUp"]==1)
						{
							echo '<h2 style="margin:0px;">1   Pickup address</h2>';
							echo '<span style="padding:0px;margin:0px;">49 street, Quang Thanh town, Nghia Thanh, Chau Dus district, Ba Ria-Vung Tau province, Vietnam</span>';
						}
						else
						{
							echo '<h2 style="margin:0px;">1   Shipping address</h2>';
							$shipment = getShipmentInfo($orderId);
							$shippingAddress = $shipment['address'];
							$customer = getCustomerInfo($_COOKIE["customerId"]);
							$customerName = $customer["firstName"]." ".$customer["lastName"];
							$shippingInstruction = $shipment["shippingInstruction"];

							echo '<div>';

							echo '<p style="padding:0px;margin:0px;">'.$customerName.'</p>';
							echo '<p style="padding:0px;margin:0px;">'.$shippingAddress.'</p>';
							echo '<p style="padding:0px;margin:0px;">'.$shippingInstruction.'</p>';

							if (!isset($_GET['updateShippingAddress']) && !isset($_GET['addShippingInstruction']))
							{	
								echo '<a style="display: block; text-decoration-line:none; color: #007185" href="proceedOrder.php?updateShippingAddress=true">Change Address</a>';
								echo '<a style="text-decoration-line:none; color: #007185" href="proceedOrder.php?addShippingInstruction=true">Add delivery instructions</a>';
							}
							else if (isset($_GET['updateShippingAddress']))
							{
								echo '<form method="POST" action="..\shipping\updateShippingAddress.php">';

								echo '<div style="display:flex; flex-direction: column; align-items: flex-end;">';
								echo '<textarea style="display:block; margin: 10px 0px;" name="updatedAddress" placeholder="New shipping Address" cols=50>';
								echo '</textarea>';
								echo '<button style="background: none; color: inherit; border: none; padding: 5px; font: inherit; cursor: pointer; outline: inherit; background-color:#FFD814; border-color: #FCD200; border-radius: 5px; margin: 0px 5px;" type="submit">Update</button>';
								echo '</div>';

								echo '</form>';
							}
							else if (isset($_GET['addShippingInstruction']))
							{
								echo '<form method="POST" action="..\shipping\addShippingInstruction.php">';

								echo '<div style="display:flex; flex-direction: column; align-items: flex-end;">';
								echo '<textarea style="display:block; margin: 10px 0px;" name="addedInstruction" placeholder="Add shipping instructions here." cols=50>';
								echo '</textarea>';
								echo '<button style="background: none; color: inherit; border: none; padding: 5px; font: inherit; cursor: pointer; outline: inherit; background-color:#FFD814; border-color: #FCD200; border-radius: 5px; margin: 0px 5px;" type="submit">Add</button>';
								echo '</div>';

								echo '</form>';
							}
							echo '</div>';
						}

					?>
				</div>

				<div id="reviewItems" style="">
					<h2 style="margin:50px 0px 20px 0px;">2   Review items</h2>
					<section style="display: flex; flex-direction: column; border: 1px solid #DDD; padding:10px; border-radius: 5px;">

					<!--Display the cart item-->
					<?php
						require_once("..\db\query.php");
						// get the user current processing order
						$orderId = getProcessingOrderWithCustomerId($_COOKIE['customerId']);
						// get a list of cart items that are currently in the cart
						$cartItemsList = getCartItemsListWithOrderId(intval($orderId));
						// loop through each cart item and display
						foreach($cartItemsList as $itemId => $itemInfo)
						{
							echo '<div style="display:flex; padding-bottom: 20px; padding-top: 20px; border-bottom: 1px solid #DDD">';

							echo '<div style="flex-basis: 25%;">';
							echo '<img style="padding: 0px 25%;" width=50% src="../img/'.$itemInfo[4].'">';
							echo '</div>';

							echo '<div style="flex-basis: 65%; padding-left: 20px; padding-right: 20px;">';
							echo '<h3 style="margin:0px;font-weight:500;padding-top: 0px; font-size: 1em;">'.$itemInfo[0].'</h3>';
							
							echo '<p>Qty: '.$itemInfo[2].'</p>';
							
							echo '</div>';

							echo '<p style="font-size: 1.2em;font-weight:700; justify-content: flex-end; margin: 0px;">$'.$itemInfo[1].'</p>';

							echo '</div>';
						}

						if (isset($_GET['itemUpdated']) && $_GET['itemUpdated']=='true')
						{
							echo '<p style="color: red;">Cart item has been updated.</p>';
						}
					?>

			</section>

				<div>
			</section>

			<section id="placeOrder">
				<div style="border: 1px solid #DDD; padding:25px; border-radius: 5px; margin-left: 100px">

				<a href="placeOrder.php" style="background-color:#FFD814; color: black;border-radius: 5px; padding: 10px 50px; text-decoration-line: none">Place your order</a>

				<?php
					echo '<h2>Order summary</h2>';

					require_once("..\db\query.php");
					$orderId = getProcessingOrderWithCustomerId($_COOKIE["customerId"]);
					$subtotal = getOrderPriceSubtotal($orderId);
					echo '<div style="display:flex; justify-content:space-between;">';
					echo '<p style="margin: 0px; padding-bottom: 4px;">Items:</p>';
					echo '<p style="margin: 0px; padding-bottom: 4px;">$'.$subtotal.'</p>';
					echo '</div>';

					$shippingCost = 2;
					echo '<div style="display:flex; justify-content:space-between; border-bottom: 1px solid #DDD">';
					echo '<p style="margin: 0px; padding-bottom: 10px;">Shipping and handling: </p>';
					echo '<p style="margin: 0px; padding-bottom: 10px;">$'.$shippingCost.'</p>';
					echo '</div>';

					echo '<div style="display:flex; justify-content:space-between;">';
					echo '<p style="margin: 0px; padding-top: 10px; color: #B12704; font-weight: 600;">Order total: </p>';
					echo '<p style="margin: 0px; padding-top: 10px; color: #B12704; font-weight: 600;">$'.($subtotal+$shippingCost).'</p>';
					echo '</div>';
				?>

				</div>
			</section>
		</main>
	</body>
</html>

