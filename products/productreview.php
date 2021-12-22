<!DOCTYPE html>
<html>
	<head>
		<title>Healthy shopping - Product Review</title>
		<meta name="description" content="This is a page for users to write a review about the product">
		<link rel="stylesheet" href="">
	</head>
	<body style="margin: 0px; font-family: Arial,sans-serif;">
		<nav style="background-color:#04AA6D;">
			<ul style="display:flex; list-style-type: none; justify-content: flex-end; margin:0px;">
				<li style="padding:15px 30px;"><a style="color:white;" href="..\index.php">Home</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="..\logout.php?action=index.php">Sign-out</a></li>';
				<li style="padding:15px 30px;"><a style="color:white;" href="..\orders\orders.php">Orders</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="..\carts\cartView.php">Cart</a></li>
			</ul>
		</nav>

		<main style="width: 60%; margin: 30px auto; display: flex; flex-direction: column;">
			<h1 style="font-size: 1.8em;">Create Review</h1>

			<?php
			require_once("..\db\query.php");
			$productId = isset($_POST['productId'])? $_POST['productId']:$_GET['productId'];
			$productInfo = getProductInfo($productId);
			$productName = $productInfo["productName"];
			$productImage = $productInfo["productImage"];

			echo '<form method=POST action="submitproductreview.php">';

			echo '<div style="display: flex; align-items: center; padding: 20px 0px; border-bottom: solid 1px #D5D9D9;">';
			echo '<img style="width:10%; margin: 0 20px;" src="../img/'.$productImage.'">';
			echo '<input type="text" style="display:none;" name="productId" value="'.$productId.'">';
			echo '<p style="margin-left: 50px; color: #0F1111;">'.$productName.'</p>';
			echo '</div>';

			echo '<div style="padding: 20px 0px; border-bottom: solid 1px #D5D9D9;">';
			echo '<h2 style="font-size: 1.3em; margin-top: 0px;">Overall rating</h2>';
			echo '<input name="rating" type="number" max=5 min=0 step=0.5 style="width: 50px;"></input>';
			echo '</div>';

			echo '<div style="padding: 20px 0px; border-bottom: solid 1px #D5D9D9;"s>'; 
			echo '<h2 style="font-size: 1.3em; margin-top: 0px;">Add a headline</h2>';
			echo '<input type="input" name="headline" placeholder="What is the most important to know?" style="width: 80%; padding: 10px;">';
			echo '</div>';

			echo '<div style="padding: 20px 0px;">';
			echo '<h2 style="font-size: 1.3em; margin-top: 0px;">Add a written review</h2>';
			echo '<textarea name="review" placeholder="What did you like or dislike? What did you use the product for?" style="width: 80%; padding: 10px;"></textarea>';
			echo '</div>';

			echo '<button type="submit" style="color: black; background-color:#FFD814; border-color: #FCD200; border-radius: 8px; padding: 10px; text-decoration-line: none; margin-right: 20px; margin-top: 20px; cursor: pointer; border: none;" name="submit" value="clicked">Submit</button>';

			echo '</form>';			

			?>

		</main>

	</body>
</html>