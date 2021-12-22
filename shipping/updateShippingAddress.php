<?php
require_once("..\db\query.php");
$orderId = getProcessingOrderWithCustomerId($_COOKIE['customerId']);
updateShippingAddress($orderId, $_POST['updatedAddress']);
header("Location: ..\orders\proceedOrder.php");
?>
