<?php

class DbConfig
{
	/*
	@user -> Database username
	@password -> Database password
	@dbname -> Database name
	@host -> Database host
	@dbh -> PDO class object
	*/
	private $user;
	private $password;
	private $dbname;
	private $host;
	private static;

	function __construct()
	{
		$this->user = "root";
		$this->password = "";
		$this->dbname = "scram";
		$this->host = "127.0.0.1";
	}

	/*
	Database connection function
	@return @dbh
	*/
	public function db_connect()
	{
		/*
		@db -> Database connection string
		*/
		$db = "mysql:dbname=".$this->dbname.";host=".$this->host."";
		try
		{
		    self::$dbh = new PDO($db, $this->user, $this->password);
		    self::$dbh->exec("SET NAMES 'utf8'; SET CHARSET 'utf8'");
		}
		catch (PDOException $e)
		{
		    print_r("<pre>");
		    print_r("Connection Failed : ".$e->getMessage());
		    print_r("</pre>");
		}
		return self::$dbh;
	}

	public function db_disconnect()
	{
		self::$dbh = null;
	}
}
?>