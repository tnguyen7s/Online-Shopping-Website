<?php
	require_once("..\db\query.php");
	require_once("..\app_enums\orderstatusenum.php");

	updateOrderStatus($_POST["orderId"], OrderStatusEnum::CancellationRequested);
	header("Location: orders.php?requestCancellation=true");
?>