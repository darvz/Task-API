<?php 
declare(strict_types=1);

require __DIR__ ."/bootstrap.php";

$dbConnect = new DbConnect($_ENV["DB_HOST"],$_ENV["DB_NAME"],$_ENV["DB_USER"],$_ENV["DB_PASS"]);

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", $path);

$resource = $parts[2];
$id = $parts[3] ?? null;

$gateway = new UserGateway($dbConnect);
$auth = new Auth($gateway);

if (!$auth->AuthenticateAPIKey()) 
{
	exit;
}

$userId = $auth->GetUserId();

if ($resource != "Task") 
{
	http_response_code(404);
	exit;
}
else 
{
	header("Content-Type: application/json; charset=UTF-8");

	$gateway = new TaskGateway($dbConnect);
	$controller = new TaskController($gateway, $userId);
	$controller->ProcessRequest($_SERVER['REQUEST_METHOD'], $id);
}