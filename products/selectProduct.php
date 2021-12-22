<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Home</title>
		<meta name="description" content="This is a page to display the product detail">
		<link rel="stylesheet" href="">
	</head>
	<body style="margin:0px; font-family: Arial,sans-serif;">
		<nav style="background-color:#04AA6D;">
			<ul style="display:flex; list-style-type: none; justify-content: flex-end; margin:0px;">
				<li style="padding:15px 30px;"><a style="color:white;" href="..\index.php">Home</a></li>
				<?php
				require_once("..\checkUserLogin.php");
				if (!hasLogined())
				{
					echo '<li style="padding:15px 30px;"><a style="color:white;" href="..\login.php?action=products\selectProduct.php?productId='.$_GET['productId'].'">Sign-in</a></li>';
				}
				else
				{
					echo '<li style="padding:15px 30px;"><a style="color:white;" href="..\logout.php?action=products\selectProduct.php?productId='.$_GET['productId'].'">Sign-out</a></li>';
				}
				?>
				<li style="padding:15px 30px;"><a style="color:white;" href="..\orders\orders.php">Orders</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="..\carts\cartView.php">Cart</a></li>
			</ul>
		</nav>
		<header>
			<h1 style="text-align: center;margin:50px auto;">Healthy Shopping</h1>
		</header>

		<main style="display: flex;">
			<div style="flex-basis: 30%;">
			<?php
				require_once("..\db\query.php");
				$img_file = getProductInfo(intval($_GET['productId']))["productImage"];
				echo '<img style="margin: 0px auto; padding: 0px 15%;" width=70% src="../img/'.$img_file.'">';
			?>
			</div>

			<section style="flex-basis: 40%; margin: 0px 110px;">
			<?php
					require_once("..\db\query.php");
					$result =getProductInfo(intval($_GET['productId']));


					echo '<h2>'.$result["productName"].'</h2>';

					// html to display the product's data
					echo '<table>';
					echo '<tr>';
					echo '<th style="text-align:left; padding-bottom:20px;">Brand</th>';
					echo '<td style="text-align:left padding-bottom: 20px;">'.$result["brand"].'</td>';
					echo '</tr>';

					echo '<tr>';
					echo '<th colspan="2" style="text-align:left">About this item:</th>';
					echo '</tr>';
					echo '<tr>';
					echo '<td colspan="2" style="text-align:left">'.$result["productDetails"].'</td>';
					echo '</tr>';
					echo '</table>';
			?>
			</section>

			<section>
			<form method="POST" action="..\carts\addItemToCart.php">
				<?php
				$result =getProductInfo(intval($_GET['productId']));

				echo '<input style = "display:none;" type="text" name="productId" value="'.$_GET['productId'].'"></input>';

				// display price
				echo '<p>Price</p>';
				echo '<p style="color:red;">'.$result["retailPrice"].'</p>';

				// Display the count.
				if (intval($result["inStockQuantity"])==0)
				{
					echo '<p style="color: gray;">Out of Stock.</p>';
				}
				else
				{
					echo '<p style="color: green;">In Stock.</p>';
					echo '<select name="itemsCount">';
					for ($i=1; $i<=$result["inStockQuantity"]; $i++)
					{
						echo '<option value="'.strval($i).'">'.strval($i).'</option>';
					}
					echo '</select>';

					// Display button.
					echo '<button type="submit"> Add to Cart</button>';
				}
				?>
			</form>
				<?php
					if (isset($_GET['addedToCart']) && $_GET['addedToCart']=="true")
					{
						echo '<p style="color: green; font-weight: 700px;">Added To Cart</p>';
					}
				?>
			</section>
		</main>
	</body>
</html>