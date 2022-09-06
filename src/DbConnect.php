<?php 

class DbConnect {

	public function __construct(
		private string $host,
		private string $name, 
		private string $username,
		private string $password
	){

	}

	public function GetConnection(): PDO
	{
		$dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

		return new PDO($dsn, $this->username, $this->password, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_STRINGIFY_FETCHES => false
		]);
	}
}

 ?>