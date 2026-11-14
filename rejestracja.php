<?php
$conn = mysqli_connect("localhost", "root", "", "baza");

if($_POST){
	$user = $_POST['user'];
	$password = $_POST['password'];

	mysqli_query($conn, "INSERT INTO users (user, password) VALUES ('$user', '$password')");
	header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<title>Register</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<div class="form-box">
		<h1>Rejestracja</h1>
		<form method="POST">
			<input type="text" name="user" placeholder="login">
			<input type="password" name="password" placeholder="haslo">
			<input type="submit" value="zarejestruj">
		</form>
		<p><a href="logowanie.php">powrot do logowania</a></p>
	</div>
</body>
</html>