<?php 

class UserGateway
{
	private PDO $conn;
	public function __construct(DbConnect $dbConnect)
	{
		$this->conn = $dbConnect->GetConnection();
	}

	public function GetByApiKey(string $key): array | false
	{
		$sql = "SELECT *
				FROM testuser 
				WHERE ApiKey = :apiKey";

		$stmt = $this->conn->prepare($sql);

		$stmt->bindValue(":apiKey", $key, PDO::PARAM_STR);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}