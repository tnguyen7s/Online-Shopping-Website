<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Cancelled Orders</title>
		<meta name="description" content="This is a page to display the customer's cancelled orders">
		<link rel="stylesheet" href="">
	</head>
	<body style="margin:0px; font-family: Arial,sans-serif;">
		<nav style="background-color:#04AA6D;">
			<ul style="display:flex; list-style-type: none; justify-content: flex-end; margin:0px;">
				<li style="padding:15px 30px;"><a style="color:white;" href="../index.php">Home</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="../logout.php?action=index.php">Sign-out</a></li>';
				<li style="padding:15px 30px;"><a style="color:white;" href="orders.php">Orders</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="../carts\cartView.php">Cart</a></li>
			</ul>
		</nav>

		<h1 style="margin: 100px 15% 30px 15%; font-weight:  600">Your orders</h1>


		<nav style="margin: 20px 15%; padding: 0px; border-bottom: solid 1px #DDD;">
			<ul style="display: flex; list-style-type: none; justify-content: flex-start; margin: 0px;"> 
				<li style="padding:5px 30px;"><a style="text-decoration-line:none; color: #007185;" href="orders.php">Orders</a></li>
				<li style="padding:5px 30px; border-bottom: solid 2px #B12704;"><a style="text-decoration-line:none; color: #007185; color:black; font-weight: 600;" href="notYetShippedOrders.php">Not yet shipped</a></li>
				<li style="padding:5px 30px;"><a style="text-decoration-line:none; color: #007185;" href="cancelledOrders.php">Cancelled</a></li>
				<li style="padding:5px 30px;"><a style="text-decoration-line:none; color: #007185;" href="returnedItems.php">Returned</a></li>
			</ul>
		</nav>

		<main style="margin: 0px 15%; display: flex; flex-direction: column; font-size: 0.8em">
			<?php
			require_once("..\db\query.php");
			require_once("..\app_enums\shippingstatusenum.php");
			require_once("..\app_enums\orderstatusenum.php");
			// get customer's id
			$customerId = $_COOKIE['customerId'];

			// get all customer's orders that have been placed
			$orders = getCancelledOrdersFromCustomer($customerId);

			// get customer's name 
			$customer = getCustomerInfo($customerId);
			$customerName = $customer["firstName"]." ".$customer["lastName"];

			// get the subtotal of each order
			foreach ($orders as $orderId => $orderInfo)
			{
				$shippingCost = isset($orders[$orderId]["shippingCost"])?$orders[$orderId]["shippingCost"]:0;
				$orders[$orderId]["total"] = $shippingCost + getOrderPriceSubtotal($orderId);
			}
			// display each order
			foreach ($orders as $orderId => $orderInfo)
			{
				if ($orderId!=null){
					echo '<div class="order" style="display:flex; flex-direction: column; border: solid 1px #D5D9D9; border-radius: 5px; margin-bottom: 20px;">';
					
					echo '<div class="generalOrderInfo" style="display:flex; width: 100%-10px; color: #565959; background-color: #F0F2F2; padding: 10px; font-weight: 500; padding: 20px;">';

						echo '<div class="placedDate" style="display: flex; flex-direction: column; width: 20%;">';
						echo '<p style="margin:0px;">ORDER PLACED</p>';
						echo '<p style="margin:0px;">'.$orderInfo["placedOn"].'</p>';
						echo '</div>';

						echo '<div class="total" style="display: flex; flex-direction: column; width: 10%;">';
						echo '<p style="margin:0px;">TOTAL</p>';
						echo '<p style="margin:0px;">'.$orderInfo["total"].'</p>';
						echo '</div>';

						echo '<div class="shippingAddress" style="display: flex; flex-direction: column; width: 60%; padding-right: 10px;">';
						$tmp = ($orderInfo["isShipping"]==1)?"SHIP TO":"PICK UP";
						echo '<p style="margin:0px;">'.$tmp.'</p>';
						if ($orderInfo["isShipping"]==1){
							echo '<p style="margin:0px;">'.$customerName.'</p>';
							echo '<p style="margin:0px; padding-right: 20%;">'.$orderInfo["address"].'</p>';
						}
						else
						{
							echo '<p style="margin:0px; padding-right: 20%;">49 street, Quang Thanh town, Nghia Thanh, Chau Duc district, Ba Ria-Vung Tau province, Vietnam</p>';
						}
						echo '</div>';

						echo '<div class="orderNumber" style="display: flex; flex-direction: column; width: 10%;">';
						echo '<p style="margin:0px;">ORDER # '.$orderId.'</p>';
						echo '</div>';

					echo '</div>';

					echo '<div class="moreAboutOrder" style="display: flex; width: 100%; justify-content: space-around; margin: 5px;">';
						echo '<div class="items" style="display: flex; flex-direction:column; width: 65%;">';
						$items = getCartItemsListWithOrderId($orderId);
						foreach ($items as $itemId => $itemInfo) {
							echo '<div style="display:flex; padding: 10px; align-items: center;">';
							echo '<div style="flex-basis: 20%;">';
							echo '<img style="padding: 0px 10%;" width=80% src="../img/'.$itemInfo[4].'">';
							echo '</div>';
							echo '<div style="flex-basis: 65%; padding-left: 20px; padding-right: 20px;">';
							echo '<a style="margin:0px;font-weight:600;padding-top: 0px; font-size: 1em; text-decoration-line: none; color: #007185;" href="..\products\selectProduct.php?productId='.$itemId.'">'.$itemInfo[0].'</a>';	
							echo '<p>Qty: '.$itemInfo[2].'</p>';
							echo '<div class="twoButtonsForItem" style ="padding: 10px 0px; display: flex;">';
							
							echo '</div>';
							echo '</div>';
							echo '<p style="font-size: 1.2em;font-weight:700; justify-content: flex-end; margin: 0px;">$'.$itemInfo[1].'</p>';
							echo '</div>';

						}
						echo '</div>';

						echo '<div class="moreAboutOrderGeneralInfo" style="display: flex; flex-direction:column; width: 30%;">';

						$orderStatusId = $orderInfo["orderStatusId"];
						if ($orderStatusId==OrderStatusEnum::CancellationRequested)
						{
							$orderInfo["orderStatus"] = "Cancellation requested.";
						}
						else if ($orderStatusId==OrderStatusEnum::IsCancelled)
						{
							$orderInfo["orderStatus"] = "Transaction has been cancelled.";
						}

						echo '<h3 style="color: #002F36">'.$orderInfo["orderStatus"].'</h3>';

						if ($orderStatusId==OrderStatusEnum::CancellationRequested){
							if ($orderInfo["isShipping"]==1 && $orderInfo["shippingStatus"]==ShippingStatus::ShippingCompleted)
							{
								echo '<h3 style="color: #002F36; margin-top: 0px;">Delivered '.$orderInfo["deliveredTime"].'</h3>';
							}
							else if ($orderInfo["isShipping"]==1 && $orderInfo["shippingStatus"]==ShippingStatus::ShippingIncompleted)
							{
								echo '<h3 style="color: #002F36; margin-top: 0px;">Estimate delivered '.$orderInfo["deliveredTime"].'</h3>';
							}
							else if ($orderInfo["isShipping"]==1 && $orderInfo["shippingStatus"]==ShippingStatus::ShippingDelayed)
							{
								echo '<h3 style="color: #002F36; margin-top: 0px;">Delay delivered to'.$orderInfo["deliveredTime"].'</h3>';
							} 
						}

						echo '<form method=POST action="leavesellermsg.php">';
						echo '<input type="text" style="display:none" name="orderId" value="'.$orderId.'">';
						echo '<button type="submit" style="color: black; background-color: transparent; padding: 5px 50px; border: solid 1px #D5D9D9; border-radius: 8px; box-shadow: 0 2px 5px 0 rgba(213,217,217,.5); display: inline-block;text-align: center; margin-bottom: 10px; cursor: pointer">Leave seller message</button>';
						echo '</form>';

						echo '</div>';
					echo '</div>';

					echo '</div>';
				}
			}
			?>

		</main>
	</body>
</html>