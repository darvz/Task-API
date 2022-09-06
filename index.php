<?php 
declare(strict_types=1);
require dirname(__DIR__) . "/api/vendor/autoload.php";

set_error_handler("ErrorHandler::HandleError");
set_exception_handler("ErrorHandler::HandleException");

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__) ."/api");
$dotenv->load();

$dbConnect = new DbConnect($_ENV["DB_HOST"],$_ENV["DB_NAME"],$_ENV["DB_USER"],$_ENV["DB_PASS"]);

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", $path);

$resource = $parts[2];
$id = $parts[3] ?? null;

if ($resource != "Task") {
	http_response_code(404);
	exit;
}
else 
{
	header("Content-Type: application/json; charset=UTF-8");
	
	$gateway = new TaskGateway($dbConnect);
	$controller = new TaskController($gateway);
	$controller->ProcessRequest($_SERVER['REQUEST_METHOD'], $id);
}