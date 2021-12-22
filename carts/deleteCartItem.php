<?php
	require_once("..\db\query.php");
	$orderId = $_GET['orderId'];
	$productId = $_GET['productId'];

	if (deleteCartItem($orderId, $productId))
	{
		header("Location: cartView.php?itemUpdated=true");
	}
?>