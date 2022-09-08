<?php 

class Auth
{
	private $userId;

	public function __construct(private UserGateway $gateway){}

	public function AuthenticateAPIKey(): bool
	{
		if (empty($_SERVER["HTTP_X_API_KEY"])) 
		{
			header("Content-Type: application/json; charset=UTF-8");

			http_response_code(404);
			echo json_encode(["message" => "Missing API key"]);
			return false;
		}

		$apiKey = $_SERVER["HTTP_X_API_KEY"];

		$user = $this->gateway->GetByApiKey($apiKey);

		if ($user === false) 
		{
			header("Content-Type: application/json; charset=UTF-8");

			http_response_code(401);
			echo json_encode(["message" => "Invalid API key"]);
			return false;
		}

		if ( $user["UserType"] === 0) 
		{
			$this->userId = $user["UserType"];
		}
		else 
		{
			$this->userId = $user["Id"];
		}
		

		return true;
	}

	public function GetUserId(): int
	{
		return $this->userId;
	}
}