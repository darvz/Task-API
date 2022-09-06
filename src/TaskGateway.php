<?php 

class TaskGateway
{
	private PDO $conn;
	public function __construct(DbConnect $dbConnect)
	{
		$this->conn = $dbConnect->GetConnection();
	}

	public function GetAll(): array
	{
		$sql = "SELECT * 
				FROM task 
				ORDER BY Id";

		$stmt = $this->conn->query($sql);
		$data = [];

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['IsComplete'] = (bool) $row['IsComplete'];
			$data[] = $row;
		}

		return $data;
	}

	public function GetById(string $id): array | false
	{
		$sql = "SELECT * 
				FROM task 
				WHERE id = :id";

		$stmt = $this->conn->prepare($sql);
		$stmt->bindValue("id", $id, PDO::PARAM_INT);
		$stmt->execute();

		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($data !== false) {
			$data['IsComplete'] = (bool) $data['IsComplete'];
		}

		return $data;
	}

	public function Insert(array $data) : string
	{
		$sql = "INSERT INTO task (name, priority, iscomplete)
				VALUES (:name, :priority, :iscomplete)";

		$stmt = $this->conn->prepare($sql);
		
		$stmt->bindValue(":name", $data["Name"], PDO::PARAM_STR);
		$stmt->bindValue(":iscomplete", $data["iscomplete"] ?? false, PDO::PARAM_BOOL);

		if (empty($data["Priority"])) 
		{
			$stmt->bindValue(":priority", null, PDO::PARAM_STR);
		}
		else 
		{
			$stmt->bindValue(":priority", $data["Priority"], PDO::PARAM_INT);
		}

		$stmt->execute();

		return $this->conn->lastInsertId();
	}

	public function Update(string $id, array $data) : int
	{
		$field = [];

		if (array_key_exists("Name", $data)) 
		{
			$field["Name"] = [
				$data["Name"], 
				PDO::PARAM_STR
			];
		}

		if (array_key_exists("Priority", $data)) 
		{
			$field["Priority"] = [
				$data["Priority"],
				$data["Priority"] === null ? 
				PDO::PARAM_NULL : PDO::PARAM_INT
			];
		}

		if (array_key_exists("IsComplete", $data)) 
		{
			$field["IsComplete"] = [
				$data["IsComplete"], 
				PDO::PARAM_INT
			];
		}

		if (empty($field)) {
			return 0;
		}
		else 
		{
			if (array_key_exists("IsComplete", $data)) 
			{
				$field["IsComplete"] =  [$data["IsComplete"], PDO::PARAM_BOOL];
			}

			$sets = array_map(function($value) {
				return "$value = :$value";
			}, array_keys($field));

			$sql = "UPDATE task"
				 . " SET " . implode(", ", $sets)
				 . " WHERE Id = :Id";

			$stmt = $this->conn->prepare($sql);
			$stmt->bindValue(":Id", $id, PDO::PARAM_INT);
			
			foreach ($field as $name => $values) {
				$stmt->bindValue(":$name", $values[0], $values[1]);
			}

			$stmt->execute();
			return $stmt->rowCount();
		}
	}

	public function Delete(string $id): int
	{
		$sql = "DELETE FROM task 
				WHERE id = :id";

		$stmt = $this->conn->prepare($sql);
		$stmt->bindValue(":id", $id, PDO::PARAM_INT);

		$stmt->execute();

		return $stmt->rowCount();
	}
}