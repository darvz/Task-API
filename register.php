<?php 

require __DIR__ . "/vendor/autoload.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$dbConnect = new DbConnect($_ENV["DB_HOST"],$_ENV["DB_NAME"],$_ENV["DB_USER"],$_ENV["DB_PASS"]);
	$conn = $dbConnect->GetConnection();

	$sql = "INSERT INTO testuser (Name, Username, Pass, ApiKey) VALUES (:name, :username, :pass, :apikey)";

	$stmt = $conn->prepare($sql);

	$password = password_hash($_POST['username'], PASSWORD_DEFAULT);
	$apiKey = bin2hex(random_bytes(16));

	$stmt->bindValue(":name", $_POST['name'], PDO::PARAM_STR);
	$stmt->bindValue(":username", $_POST['username'], PDO::PARAM_STR);
	$stmt->bindValue(":pass", $password, PDO::PARAM_STR);
	$stmt->bindValue(":apikey", $apiKey, PDO::PARAM_STR);

	$stmt->execute();

	echo "Tangina mo Jepoy Dizon!! Baho ng tite mo ",$apiKey;
	exit;
}

 ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>REGISTER</title>
	<link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
	<main>
		<h1>Register</h1>
		<form method="post">
			<label for="name">
				Name
				<input id="name" name="name">
			</label>
			<label for="username">
				Username
				<input id="username" name="username">
			</label>
			<label for="password">
				Password
				<input type="password" id="password" name="password">
			</label>
			<button>Register</button>
		</form>
	</main>
</body>
</html>