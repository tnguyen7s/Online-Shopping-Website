<?php
require_once("..\db\query.php");
$orderId = getProcessingOrderWithCustomerId($_COOKIE['customerId']);
updateShippingInstruction($orderId, $_POST['addedInstruction']);
header("Location: ..\orders\proceedOrder.php");
?>


