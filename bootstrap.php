<?php 

require dirname(__DIR__) . "/api/vendor/autoload.php";

set_error_handler("ErrorHandler::HandleError");
set_exception_handler("ErrorHandler::HandleException");

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__) ."/api");
$dotenv->load();