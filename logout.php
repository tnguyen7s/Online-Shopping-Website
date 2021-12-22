<?php
setcookie("sessionId", time()-36000);
setcookie("customerId", time()-36000);
if (isset($_GET['action']))
{
	header("Location: ".$_GET['action']);
}
?>