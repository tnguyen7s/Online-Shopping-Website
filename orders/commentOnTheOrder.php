<?php
	require_once("..\db\query.php");
	insertCommentOnAnItem($_POST['orderId'], $_POST['productId'], $_POST['comment'], date('Y-m-d H:i:s'));
?>