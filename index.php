<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Healthy shopping - Home</title>
		<meta name="description" content="This is a homepage to display the products">
		<link rel="stylesheet" href="">
	</head>
	<body style="font-family: Arial,sans-serif; margin: 0px;">
		<nav style="background-color:#04AA6D;">
			<ul style="display:flex; list-style-type: none; justify-content: flex-end; margin:0px;">
				<?php
				require_once("checkUserLogin.php");
				if (!hasLogined())
				{
					echo '<li style="padding:15px 30px;"><a style="color:white;" href="login.php">Sign-in</a></li>';
				}
				else
				{
					echo '<li style="padding:15px 30px;"><a style="color:white;" href="logout.php?action=index.php">Sign-out</a></li>';
				}
				?>
				<li style="padding:15px 30px;"><a style="color:white;" href="orders\orders.php">Orders</a></li>
				<li style="padding:15px 30px;"><a style="color:white;" href="carts\cartView.php">Cart</a></li>
			</ul>
		</nav>
		<header>
			<h1 style="text-align: center;margin:50px auto;">Healthy Shopping</h1>
		<header>
		<main style="display: grid; grid-template: repeat(5, 250px)/repeat(3, auto); grid-gap: 20px; margin: 50px; ">
		<?php
			require_once("db\query.php");

			$resultSet = getAllProductNamesAndProductIDs();
			foreach ($resultSet as $key => $val)
			{
				echo '<div style="display:flex; flex-direction: column;">';
				echo '<img style="padding: 0px 42%;" width=16% height=120px src="img/'.$val['img'].'">';
				echo '<p style="font-weight: 600; color: #B12704;">$'.$val['price'].'</p>';
				echo '<a style="text-decoration-line: none; color: black;" href="products\selectProduct.php?productId='.$key.'">'.$val['name'].'</a>';
				echo '</div>';
			}
		?>
		</main>
	</body>
</html>