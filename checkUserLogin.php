<?php
function hasLogined()
{
	// get sessionId and customerId that may have stored in the cookies
	if (!isset($_COOKIE['sessionId']) && !isset($_COOKIE['customerId']))
	{
		return false;
	}
	$sessionId = $_COOKIE['sessionId'];
	$customerId = $_COOKIE['customerId'];

	//get the database connection
	require_once("db\mysql_connect.php");
	$db = new DatabaseContext();
	$dbc = $db -> connect();

	if ($sessionId!=null && $customerId!=null)
	{
		$query = "SELECT * FROM Sessions WHERE sessionId=? AND customerId=?";

		$stmt = mysqli_prepare($dbc, $query);

		if ($stmt==false)
		{
			mysqli_close($dbc);
			throw new Exception(mysqli_error($dbc));
		}
		else
		{
			mysqli_stmt_bind_param($stmt, "ii", $sessionId, $customerId);

			mysqli_stmt_execute($stmt);

			mysqli_stmt_bind_result($stmt, $sessionIdFromDb, $customerIdFromDb);

			mysqli_stmt_fetch($stmt);

			if ($sessionIdFromDb!=null && $customerIdFromDb!=null)
			{
				// there is session that matches with the user Id, user has a logined session.
				mysqli_close($dbc);
				return true;
			}
		}
	}

	mysqli_close($dbc);

	//user has not logined to the system yet
	return false;
}
?>