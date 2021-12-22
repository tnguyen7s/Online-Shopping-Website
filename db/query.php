<?php
	/*
	Check if user has a valid login information
	@args phone: the user's phone number.
	@args pwd: the user's password.
	@returns the customer id if user has logined successfully; otherwise returns null;
	*/
	function isValidLogin($phone, $pwd)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db->connect();

		// create the query
		$query = "SELECT CustomerId FROM customers WHERE PhoneNumber=? AND Password =?;";

		// prepare the statement
		$stmt = mysqli_prepare($dbc, $query);

		$customerId = null;
		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "ss", $phone, $pwd);

			$success = mysqli_stmt_execute($stmt);

			if ($success==true)
			{
				mysqli_stmt_bind_result($stmt, $customerId);

				mysqli_stmt_fetch($stmt);
			}
			else
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
		}

		mysqli_close($dbc);
		return $customerId;
	}


	/*
	Get names and ids of all products.
	*/
	function getAllProductNamesAndProductIDs()
	{
		// get the connection and the query
		require_once('mysql_connect.php');

		$db = new DatabaseContext();
		$dbc = $db->connect();
			
		$query = "SELECT ProductName, ProductId, RetailPrice, ProductImage FROM products;";

		// send the request to DBMSs to get the data
		$response = @mysqli_query($dbc, $query);
		$resultSet = array();

		if ($response)
		{
			while ($row=mysqli_fetch_array($response))
			{
				$resultSet[$row['ProductId']] = array();
				$resultSet[$row['ProductId']]['name'] = $row["ProductName"];
				$resultSet[$row['ProductId']]['price'] = $row["RetailPrice"];
				$resultSet[$row['ProductId']]['img'] = $row["ProductImage"];
			}
			mysqli_close($dbc);
		}
		else
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}

		return $resultSet;
	}


	/*
	Get orders that have been requested to cancel or has been cancelled
	@args customerId: the id of the customer
	@returns: all the orders that are  that have been requested to cancel or has been cancelled.
	*/
	function getCancelledOrdersFromCustomer($customerId)
	{
		require_once("..\app_enums\orderstatusenum.php");
		// get the connection
		require_once("mysql_connect.php");
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create queries
		$query = "SELECT Orders.OrderId, Orders.OrderStatusId, Orders.IsShipping, Orders.PlacedOn, Shipment.ShippingStatusId, Shipment.Address, Shipment.ArrivalTime, Shipment.ShippingCost FROM Orders LEFT JOIN Shipment ON Orders.OrderId=Shipment.OrderId WHERE Orders.CustomerId=? AND OrderStatusId IN (?,?) ORDER BY Orders.PlacedOn DESC;";

		$stmt = mysqli_prepare($dbc, $query);
		$ordersList = array();
		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			$os1 = OrderStatusEnum::IsCancelled;
			$os2 = OrderStatusEnum::CancellationRequested;
			mysqli_stmt_bind_param($stmt, "iii", $customerId, $os1, $os2);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $orderId, $orderStatusId, $isShipping, $placedOn, $shippingStatusId, $address, $arrivalTime, $shippingCost);
			$ordersList[$orderId] = array();

			while (mysqli_stmt_fetch($stmt))
			{
				$ordersList[$orderId]["orderStatusId"] = $orderStatusId;
				$ordersList[$orderId]["isShipping"] = $isShipping;
				$ordersList[$orderId]["placedOn"] = ($placedOn==NULL)?"":$placedOn;
				$ordersList[$orderId]["shippingStatus"]= $shippingStatusId;
				$ordersList[$orderId]["address"] = $address;
				$ordersList[$orderId]["deliveredTime"] = $arrivalTime;
				$ordersList[$orderId]["shippingCost"] = ($shippingCost==NULL)?0:$shippingCost;
			}
		}
		mysqli_close($dbc);
		return $ordersList;
	}
	/*
	Get completed orders of a customer
	@args customerId: the id of the customer
	@returns: all the orders that are completed*/
	function getCompletedOrders($customerId)
	{
		require_once("..\app_enums\orderstatusenum.php");
		// get the connection
		require_once("mysql_connect.php");
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create queries
		$query = "SELECT Orders.OrderId, Orders.OrderStatusId, Orders.IsShipping, Orders.PlacedOn, Shipment.ShippingStatusId, Shipment.Address, Shipment.ArrivalTime, Shipment.ShippingCost FROM Orders LEFT JOIN Shipment ON Orders.OrderId=Shipment.OrderId WHERE Orders.CustomerId=? AND OrderStatusId = ? ORDER BY Orders.PlacedOn DESC;";

		$stmt = mysqli_prepare($dbc, $query);
		$ordersList = array();
		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			$os1 = OrderStatusEnum::TransactionCompleted;
			mysqli_stmt_bind_param($stmt, "ii", $customerId, $os1);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $orderId, $orderStatusId, $isShipping, $placedOn, $shippingStatusId, $address, $arrivalTime, $shippingCost);
			$ordersList[$orderId] = array();

			while (mysqli_stmt_fetch($stmt))
			{
				$ordersList[$orderId]["orderStatusId"] = $orderStatusId;
				$ordersList[$orderId]["isShipping"] = $isShipping;
				$ordersList[$orderId]["placedOn"] = ($placedOn==NULL)?"":$placedOn;
				$ordersList[$orderId]["shippingStatus"]= $shippingStatusId;
				$ordersList[$orderId]["address"] = $address;
				$ordersList[$orderId]["deliveredTime"] = $arrivalTime;
				$ordersList[$orderId]["shippingCost"] = ($shippingCost==NULL)?0:$shippingCost;
			}
		}
		mysqli_close($dbc);
		return $ordersList;
	}

	/*
	Get placed orders of a customer
	@args customerId: the id of the customer
	@returns: all the orders that are neither being processed by clients nor cancelled.
	*/
	function getPlacedOrdersFromCustomer($customerId)
	{
		require_once("..\app_enums\orderstatusenum.php");
		// get the connection
		require_once("mysql_connect.php");
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create queries
		$query = "SELECT Orders.OrderId, Orders.OrderStatusId, Orders.IsShipping, Orders.PlacedOn, Shipment.ShippingStatusId, Shipment.Address, Shipment.ArrivalTime, Shipment.ShippingCost FROM Orders LEFT JOIN Shipment ON Orders.OrderId=Shipment.OrderId WHERE Orders.CustomerId=? AND OrderStatusId IN (?,?,?) ORDER BY Orders.PlacedOn DESC;";

		$stmt = mysqli_prepare($dbc, $query);
		$ordersList = array();
		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			$os1 = OrderStatusEnum::WaitingForSellerResponse;
			$os2 = OrderStatusEnum::TransactionProcessing;
			$os3 = OrderStatusEnum::TransactionCompleted;
			mysqli_stmt_bind_param($stmt, "iiii", $customerId, $os1, $os2, $os3);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $orderId, $orderStatusId, $isShipping, $placedOn, $shippingStatusId, $address, $arrivalTime, $shippingCost);
			$ordersList[$orderId] = array();

			while (mysqli_stmt_fetch($stmt))
			{
				$ordersList[$orderId]["orderStatusId"] = $orderStatusId;
				$ordersList[$orderId]["isShipping"] = $isShipping;
				$ordersList[$orderId]["placedOn"] = ($placedOn==NULL)?"":$placedOn;
				$ordersList[$orderId]["shippingStatus"]= $shippingStatusId;
				$ordersList[$orderId]["address"] = $address;
				$ordersList[$orderId]["deliveredTime"] = $arrivalTime;
				$ordersList[$orderId]["shippingCost"] = ($shippingCost==NULL)?0:$shippingCost;
			}
		}
		mysqli_close($dbc);
		return $ordersList;
	}

	/*
	Get all cart items in user's current cart
	@args orderId: the id of the order
	@returns: a list of items with price.
	*/
	function getCartItemsListWithOrderId(int $orderId)
	{
		// get the connection and the query
		require_once('mysql_connect.php');

		$db = new DatabaseContext();
		$dbc = $db->connect();

		// create the query
		$query = "SELECT Products.ProductId, Products.ProductName, Products.RetailPrice, CartItems.Quantity, Products.InStockQuantity, Products.ProductImage FROM CartItems, Products WHERE CartItems.ProductId=Products.ProductId AND CartItems.OrderId=?;";

		// prepare the statement
		$stmt = mysqli_prepare($dbc, $query);

		$result = array();
		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "i", $orderId);
			$succeed = mysqli_stmt_execute($stmt);

			if (!$succeed)
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
			else
			{
				mysqli_stmt_bind_result($stmt, $productId, $productName, $price, $quantity, $instockQuantity, $productImage);


				while(mysqli_stmt_fetch($stmt))
				{
					if ($quantity>$instockQuantity)
					{
						updateCartItemQuantity($productId, $orderId, $instockQuantity);
						$quantity = $inStockQuantity;
					}

					$result[$productId] = array($productName, $price, $quantity, $instockQuantity, $productImage);
				}
			}
		}


		mysqli_close($dbc);
		return $result;
	}

	/*
	Get comments about an item in an order
	@args orderId: the id of the order
	@args productId: the id of the product that is commented
	@returns the comments about the item in the order
	*/
	function getCommentsAboutItemInTheOrder($orderId, $productId)
	{
		// get the connection and the query
		require_once('mysql_connect.php');

		$db = new DatabaseContext();
		$dbc = $db->connect();

		// save all comments in this array
		$comments = array();

		// create the query to get comments 
		$query = "SELECT * FROM ordercomments WHERE OrderId=? AND ProductId=? ORDER BY CommentDateTime;";

		// prepare the statement
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "ii", $orderId, $productId);
			$succeed = mysqli_stmt_execute($stmt);

			if (!$succeed)
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
			else
			{
				mysqli_stmt_bind_result($stmt, $commentId, $orderId, $customerId, $comment, $commentDateTime, $productId, $ownerId);

				while(mysqli_stmt_fetch($stmt))
				{
					$comments[$commentId]= array();
					$comments[$commentId]["customerId"] = $customerId;
					$comments[$commentId]["comment"] = $comment;
					$comments[$commentId]["commentDateTime"]=$commentDateTime;
					$comments[$commentId]["ownerId"] = $ownerId;
				}
			}
		}


		mysqli_close($dbc);
		return $comments;
	}

	/*
	Get customer's information
	args customerId: the id of the customer
	returns: an associative array storing customer's info
	*/
	function getCustomerInfo($customerId)
	{
		// get the connection 
		require_once("mysql_connect.php");

		$db = new DatabaseContext();
		$dbc = $db->connect();

		// create the query
		$query = "SELECT * FROM customers WHERE CustomerId=?;";

		$stmt = mysqli_prepare($dbc, $query);

		$result = array();
		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "i", $customerId);

			$succeed = mysqli_stmt_execute($stmt);

			if ($succeed)
			{
				mysqli_stmt_bind_result($stmt, $customerId, $firstName, $lastName, $phone, $address ,$pwd);

				mysqli_stmt_fetch($stmt);

				$result['customerId'] = $customerId;
				$result['firstName'] = $firstName;
				$result['lastName'] = $lastName;
				$result['phone'] = $phone;
				$result['address'] = $address;
			}
			else
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
		}
		mysqli_close($dbc);
		return $result;
	}


	/*
	Get customer recent session
	@args customerId: the id of the customer
	@returns recent session Id 
	*/
	function getCustomerRecentSession(int $customerId)
	{
		require_once("mysql_connect.php");

		$db = new DatabaseContext();
		$dbc = $db->connect();

		// create the query
		$query = "SELECT MAX(SessionId) FROM Sessions WHERE CustomerId=?";

		// prepare the statement
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "i", $customerId);

			$success = mysqli_stmt_execute($stmt);

			if (!$success)
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}

			mysqli_stmt_bind_result($stmt, $sessionId);
			mysqli_stmt_fetch($stmt);
		}


		mysqli_close($dbc);
		return $sessionId;
	}

	/*
	Get the subtotal of user's current order.
	@args customerId: the id of the customer.
	@returns: the total price of all items in the user's cart.
	*/
	function getOrderPriceSubtotal($orderId)
	{
		$subtotal = 0;

		// make the connection with the db
		require_once("query.php");
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		if ($orderId==null)
		{
			return 0;
		}

		$cart = getCartItemsListWithOrderId($orderId);

		foreach ($cart as $itemId=>$itemInfo)
		{
			$subtotal = $subtotal + $itemInfo[1]*$itemInfo[2];
		}

		return $subtotal;
	}

	/*
	Get the order's info
	@args orderId: the id of the order
	@returns: the order's info
	*/
	function getOrderInfo($orderId)
	{
		// make the connection with the db
		require_once("query.php");
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create query 
		$query = "SELECT * FROM orders WHERE OrderId=?";

		$stmt = mysqli_prepare($dbc, $query);
		$orderInfo = array();
		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "i", $orderId);
			$succeed = mysqli_stmt_execute($stmt);

			if ($succeed)
			{
				mysqli_stmt_bind_result($stmt, $orderId, $customerId, $orderStatusId, $isPickUp, $isShipping, $placedDate);
				mysqli_stmt_fetch($stmt);

				$orderInfo['customerId'] = $customerId;
				$orderInfo['orderStatusId'] = $orderStatusId;
				$orderInfo['isPickUp'] = $isPickUp;
				$orderInfo['isShipping'] = $isShipping;
				$orderInfo['placedDate'] = $placedDate;
			}
		}
		return $orderInfo;
	}

	/*
	Get a product information given product Id
	@args productId: the Id of the product.
	*/
	function getProductInfo(int $productId)
	{
		require_once("mysql_connect.php");

		$db = new DatabaseContext();
		$dbc = $db->connect();

		$query = "SELECT * FROM products WHERE ProductId = ?";
		//prepare stmt
		$stmt = mysqli_prepare($dbc, $query);

		$productInfo = array();
		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			// bind product id to statement
			mysqli_stmt_bind_param($stmt, "i", $productId);

			// execute the statement
			mysqli_stmt_execute($stmt);

			// define the binding mechanism
			mysqli_stmt_bind_result($stmt, $productId, $productName,  $inStockQuantity, $soldQuantity, $purchasedPrice, $retailPrice, $category, $brand, $productDetails, $usedBestBy, $ratingAverage, $productImage, $ownerId);


			// fetch the first obtained row
			mysqli_stmt_fetch($stmt);

			$productInfo["productName"] = $productName;
			$productInfo["inStockQuantity"] = $inStockQuantity;
			$productInfo["retailPrice"] = $retailPrice;
			$productInfo["brand"] = $brand;
			$productInfo["ratingAverage"] = $ratingAverage;
			$productInfo["productDetails"] = $productDetails;
			$productInfo["productImage"] = $productImage;
			$productInfo["ownerId"] = $ownerId;
		}
		mysqli_close($dbc);

		return $productInfo;
	}

	/*
	Get the newest processing order of a customer
	@args customerId: the id of the customer.
	*/
	function getProcessingOrderWithCustomerId(int $customerId)
	{
		require_once("mysql_connect.php");
		require_once("..\app_enums\orderstatusenum.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the query to get the newest processing order of a customer
		$query = "SELECT OrderId FROM Orders WHERE CustomerId=? AND OrderStatusId=?";

		$stmt = mysqli_prepare($dbc, $query);

		$orderId = null;
		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			$orderStatusId = OrderStatusEnum::IsProcessedByClient;

			mysqli_stmt_bind_param($stmt, "ii", $customerId, $orderStatusId);

			$success = mysqli_stmt_execute($stmt);

			if ($success==true)
			{
				mysqli_stmt_bind_result($stmt, $orderId);

				mysqli_stmt_fetch($stmt);
			}
			else
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
		}

		mysqli_close($dbc);
		return $orderId;
	}

	/*
	Get seller's information
	@args ownerId: the Id of the seller.
	@returns an array storing the information of the seller.
	*/
	function getSellerInfo($ownerId)
	{
		// make the connection
		require_once("query.php");
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the query
		$query = "SELECT * FROM Owners WHERE OwnerId = ?";

		$stmt = mysqli_prepare($dbc, $query);

		$ownerInfo = array();
		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "i", $ownerId);

			mysqli_stmt_execute($stmt);

			mysqli_stmt_bind_result($stmt, $ownerId, $firstName, $lastName, $phone, $address, $pwd, $shopName);

			mysqli_stmt_fetch($stmt);

			$ownerInfo["ownerId"] = $ownerId;
			$ownerInfo["firstName"] = $firstName;
			$ownerInfo["lastName"] = $lastName;
			$ownerInfo["phone"] = $phone;
			$ownerInfo["address"] = $address;
			$ownerInfo["shopName"] = $shopName;
		}

		return $ownerInfo;
	}

	/*
	Get the shipment's information given the order's id.
	@args orderId: the id of the order.
	@returns: the information of the shipment.
	*/
	function getShipmentInfo($orderId)
	{
		// make the connection with the db
		require_once("query.php");
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create query 
		$query = "SELECT * FROM Shipment WHERE OrderId=?";

		$stmt = mysqli_prepare($dbc, $query);
		$shipmentInfo = array();
		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "i", $orderId);
			$succeed = mysqli_stmt_execute($stmt);

			if ($succeed)
			{
				mysqli_stmt_bind_result($stmt, $address, $arrivalTime, $shippingStatusId, $orderId, $shippingInstruction, $shippingCost);
				mysqli_stmt_fetch($stmt);

				$shipmentInfo['address'] = $address;
				$shipmentInfo['arrivalTime'] = $arrivalTime;
				$shipmentInfo['shippingStatusId'] = $shippingStatusId;
				$shipmentInfo['shippingInstruction'] = $shippingInstruction;
				$shipmentInfo['shippingCost'] = $shippingCost;
			}
		}
		return $shipmentInfo;
	}

	/*
	Create new comment
	@args orderId: the id of the order 
	@args productId: the id of the product
	@args comment: the comment
	@commentDateTime: the datetime when the comment is made
	*/
	function insertCommentOnAnItem($orderId, $productId, $comment, $commentDateTime, $customerId)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create insert query 
		$query = "INSERT INTO ordercomments(OrderId, CustomerId, Comment, CommentDateTime, ProductId) VALUES (?, ?, ?, ?, ?);";

		// prepare the stmt
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else 
		{
			// bind the parameters to the stmt
			mysqli_stmt_bind_param($stmt, "iissi", $orderId, $customerId, $comment, $commentDateTime, $productId);

			// execute stmt
			mysqli_stmt_execute($stmt);
			$affectedRows = mysqli_stmt_affected_rows($stmt);

			// if success
			if ($affectedRows==0)
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
			mysqli_close($dbc);
		}
	}
	/*
	Create new order for a customer.
	@args customerId: the id of the customer
	*/
	function insertOrderWithCustomerId(int $customerId)
	{
		require_once("mysql_connect.php");
		require_once("..\app_enums\orderstatusenum.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create sql query to insert new order
		$query = "INSERT INTO Orders(IsPickup, IsShipping, OrderStatusId, CustomerId) VALUES (?, ?, ?, ?);";

		// prepare the statement
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else{
			$pickup = 1;
			$shipping = 0;
			$processingStatus=OrderStatusEnum::IsProcessedByClient;
			// bind the parameters to the stmt
			mysqli_stmt_bind_param($stmt, "iiii", $pickup, $shipping, $processingStatus, $customerId);

			// execute the stmt
			mysqli_stmt_execute($stmt);

			$affectedRows = mysqli_stmt_affected_rows($stmt);

			if ($affectedRows==1)
			{
				return getProcessingOrderWithCustomerId($customerId);
			}
			else
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
		}
		return null;	
	}


	/*
	Insert an item into user's cart in the database
	@args productId: the id of the product
	@args orderId: the id of the order
	@args quantity: the number of items 
	*/
	function insertCartItem(int $productId, int $orderId, int $quantity)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create insert query 
		$query = "INSERT INTO CartItems(ProductId, OrderId, Quantity) VALUES (?, ?, ?);";

		// prepare the stmt
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else 
		{
			// bind the parameters to the stmt
			mysqli_stmt_bind_param($stmt, "iii", $productId, $orderId, $quantity);

			// execute stmt
			mysqli_stmt_execute($stmt);
			$affectedRows = mysqli_stmt_affected_rows($stmt);

			// if success
			if ($affectedRows==0)
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
			mysqli_close($dbc);
		}
	}

	/*
	Insert a new customer to the database
	*/
	function insertCustomer($firstName, $lastName, $phone, $address, $pwd)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create insert query 
		$query = "INSERT INTO Customers (FirstName, LastName, PhoneNumber, Address, Password) VALUES (?, ?, ?, ?, ?);";

		// prepare the statement
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			// bind the statement with input parameters.
			mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $phone, $address, $pwd);

			// execute the statement 
			mysqli_execute($stmt);

			// get the number of row affected
			$rowsAffected = mysqli_stmt_affected_rows($stmt);

			if ($rowsAffected == 0)
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
		}
	}

	/*
	Create and insert a new session to a database
	@args customerId: the id of the customer
	@returns: session Id.
	*/
	function insertSession(int $customerId)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create query
		$query = "INSERT INTO Sessions(CustomerId) VALUES(?);";

		// prepare the query
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			// bind the parameter to the statement
			mysqli_stmt_bind_param($stmt, "i", $customerId);

			// execute the staement
			mysqli_stmt_execute($stmt);

			$rowAffected = mysqli_stmt_affected_rows($stmt);

			if ($rowAffected==0)
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
		}

		mysqli_close($dbc);
		return getCustomerRecentSession($customerId);
	}

	/*
	Insert a product review
	*/
	function insertProductReview($customerId, $productId, $rating, $review, $reviewDateTime, $headline)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create query
		$query = "INSERT INTO ProductReviews(CustomerId, ProductId, Rating, Review, ReviewDatetime, Headline) VALUES(?, ?, ?, ?, ?, ?);";

		// prepare the query
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg=mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			// bind the parameter to the statement
			mysqli_stmt_bind_param($stmt, "iidsss", $customerId, $productId, $rating, $review, $reviewDateTime, $headline);

			// execute the staement
			mysqli_stmt_execute($stmt);

			$rowAffected = mysqli_stmt_affected_rows($stmt);

			if ($rowAffected==0)
			{
				$errorMsg=mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
		}

		mysqli_close($dbc);
	}

	/*
	Update order transportation to be shipping
	Get customer address and create a shipment for the order using the default address.
	@agrs customerId: the id of the customer
	@args orderId: the id of the order
	@returns: the default address of the shipment
	*/
	function insertShipmentForOrder($customerId, $orderId)
	{
		updateOrderTransportationMethod($orderId, 0, 1);
		$address = getCustomerInfo($customerId)["address"];


		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the query
		$query = "INSERT INTO Shipment(Address, ShippingStatusId, OrderId, ShippingCost) VALUES (?,1,?, 2)";

		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "si", $address, $orderId);
			mysqli_stmt_execute($stmt);

			$rowAffected = mysqli_stmt_affected_rows($stmt);
			if ($rowAffected==0)
			{
				$errorMsg = mysqli_error($dbc);
				mysqli_close();
				throw new Exception($errorMsg);
			}
		}
	}

	function updateCartItemQuantity(int $productId, int $orderId, int $quantity)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the query
		$query = "UPDATE CartItems SET Quantity=? WHERE ProductId=? AND OrderId=?";

		// bind parameters to query and execute the query
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt == false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close();
			throw new Exception($errorMsg);
		}
		else
		{

			mysqli_stmt_bind_param($stmt, "iii", $quantity, $productId, $orderId);

			mysqli_execute($stmt);
		}
	}

	/*
	Update the order's transportation method.
	@args orderId: the id of the order
	@args isPickup: 1 for yes and 0 for no
	@args isShipping: 1 for yes and 0 for no
	*/
	function updateOrderTransportationMethod($orderId, $isPickUp, $isShipping)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the update query
		$query = "UPDATE Orders SET IsPickup=?, IsShipping=? WHERE OrderId=?";

		$stmt = mysqli_prepare($dbc, $query);
		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "iii", $isPickUp, $isShipping, $orderId);

			mysqli_stmt_execute($stmt);
		}
	}
	/*
	Update the order's status
	@agrs orderId: the Id of the order
	@args orderStatusId: the status of the order
	*/
	function updateOrderStatus($orderId, $orderStatusId)
	{
		// make the connection
		require_once("mysql_connect.php");
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the update query
		$query = "UPDATE orders SET OrderStatusId=? WHERE OrderId=?";

		// prepare the stmt
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "ii", $orderStatusId, $orderId);
			mysqli_execute($stmt);
		}
	}


	/*
	Update the order's place date
	@agrs orderId: the Id of the order
	@args date: the date when the order is placed
	*/
	function updateOrderPlaceDate($orderId, $date)
	{
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the update query
		$query = "UPDATE orders SET PlacedOn=? WHERE OrderId=?";

		// prepare the stmt
		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "si", $date, $orderId);
			mysqli_execute($stmt);
		}

	}

	/*
	To update the shipping's address.
	@args $orderId: the id of the order
	@args $updatedAddress: the updated address
	*/
	function updateShippingAddress($orderId, $updatedAddress)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the update query
		$query = "UPDATE Shipment SET address=? WHERE OrderId=?";

		$stmt = mysqli_prepare($dbc, $query);
		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "si", $updatedAddress, $orderId);

			mysqli_stmt_execute($stmt);
		}
	}

	/*
	To update the shipping's instruction.
	@args $orderId: the id of the order
	@args $instruction: the updated instruction
	*/
	function updateShippingInstruction($orderId, $updatedInstruction)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		// create the update query
		$query = "UPDATE Shipment SET shippingInstruction=? WHERE OrderId=?";

		$stmt = mysqli_prepare($dbc, $query);
		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "si", $updatedInstruction, $orderId);

			mysqli_stmt_execute($stmt);
		}
	}

	function deleteCartItem($orderId, $productId)
	{
		require_once("mysql_connect.php");

		// get the database connection
		$db = new DatabaseContext();
		$dbc = $db -> connect();

		//create the delete query
		$query = "DELETE FROM CartItems WHERE OrderId=? AND ProductId=?";

		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			$errorMsg = mysqli_error($dbc);
			mysqli_close($dbc);
			throw new Exception($errorMsg);
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "ii", $orderId, $productId);
			$succeed = mysqli_stmt_execute($stmt);

			if (!$succeed)
			{
				$errorMsg = mysqli_error($dbc);
				mysqli_close($dbc);
				throw new Exception($errorMsg);
			}
			else
			{
				return true;
			}
		}
	}
?>