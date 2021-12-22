<?php
	require_once("..\db\query.php");

	updateCartItemQuantity($_POST['productId'], $_POST['orderId'], $_POST['cartItemCount']);

	header("Location: cartView.php?itemUpdated=true");
?>