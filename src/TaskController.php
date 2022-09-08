<?php 

class TaskController
{
	public function __construct(private TaskGateway $gateway,
								private int $userId)
	{
	}

	/*PUBLIC METHOD*/
	public function ProcessRequest(string $method, ?string $id): void
	{
		if ($id === null) 
		{
			switch ($method) {
				case "GET":
					$this->ProcessMethod($method, $id);
				break;
				case "POST":
					$this->ProcessMethod($method, $id);
				break;
				default:
					$this->ResponseMethodNotAllowed("GET, PATCH, DELETE");
				break;
			}
		}
		else
		{
			$task = $this->gateway->GetById($id);
			if ($task === false) 
			{
				$this->ResponseMethodNotFound();
			}
			else
			{
				switch ($method) {
					case "GET":
						echo json_encode($task);
						break;
					case "PATCH":
						$this->ProcessMethod($method, $id);
						break;
					case "DELETE":
						$this->gateway->Delete($id);
						$this->ResponseMethodSuccess();
						break;
					default:
						$this->ResponseMethodNotAllowed("GET, PATCH, DELETE");
					break;
				}
			}
			
		}
	}
	/*PRIVATE METHOD*/
	private function ProcessMethod(string $method, ?string $id): void
	{
		$data = (array) json_decode(file_get_contents("php://input"), true);
		if ($method == "POST") 
		{
			$errors = $this->GetValidationError($data);
			if (!empty($errors)) 
			{
				$this->RespondMethodUnprocessableEntity($errors);
				return;
			}
			else 
			{
				$this->gateway->Insert($data);
				$this->ResponseMethodSuccess();
			}	
		}
		if ($method == "PATCH") 
		{
			$errors = $this->GetValidationError($data, false);
			if (!empty($errors)) 
			{
				$this->RespondMethodUnprocessableEntity($errors);
				return;
			}
			else 
			{
				$this->gateway->Update($id, $data);
				$this->ResponseMethodSuccess();
			}
		}
		if ($method == "GET") 
		{
			if ($this->userId > 0) 
			{
				echo json_encode($this->gateway->GetByUserId($this->userId));
			}
			else 
			{
				echo json_encode($this->gateway->GetAll());
			}
		}
	}

	private function ProcessUserMethod(string $method)
	{

	}

	/*ERROR METHOD*/
	private function GetValidationError(array $data, bool $isNew = true): array
	{
		$errors = [];

		if ($isNew && empty($data["Name"])) 
		{
			$errors[] = "Name is required";
		}
		if (!empty($data["Priority"])) 
		{
			if (filter_var($data["Priority"], FILTER_VALIDATE_INT) === false) {
				$errors[] = "Priority must be an integer";
			}
		}

		return $errors;
	}

	/*RESPONSE METHOD*/
	private function RespondMethodUnprocessableEntity(array $error): void
	{
		http_response_code(422);
		echo json_encode(["errors" => $error]);
	}

	private function ResponseMethodNotAllowed(string $allowed_methods): void
	{
		http_response_code(405);
		header("Allow: $allowed_methods");
	}

	private function ResponseMethodNotFound(): void
	{
		http_response_code(404);
		echo json_encode(["message" => "Task not Found"]);
	}

	private function ResponseMethodSuccess(): void
	{
		http_response_code(201);
		echo json_encode(["message" => "Success"]);
	}
}