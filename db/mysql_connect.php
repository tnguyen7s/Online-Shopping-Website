<?php
class DatabaseContext{
	private const DB_HOST = 'localhost';
	private const DB_USER =  'root';
	private const DB_PASSWORD =  '';
	private const DB_NAME = 'shoppingappdb';
	private const PORT = '3308';

	public function connect()
	{
		$dbc= @mysqli_connect(self::DB_HOST,self::DB_USER, self::DB_PASSWORD, self::DB_NAME, self::PORT)
		OR die('Could not connect to MySQL:'.mysqli_connect_error());

		return $dbc;
	}
}
?>


