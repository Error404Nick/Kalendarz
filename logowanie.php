<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "baza");

if($_POST){
	$user = $_POST['user'];
	$password = $_POST['password'];

	$q = mysqli_query($conn, "SELECT * FROM users WHERE user='$user' AND password='$password'");
	if(mysqli_num_rows($q) > 0){
		$_SESSION['user'] = $user;
		header("Location: index.php");
	}
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<title>Login</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<div>
		<h1>Logowanie</h1>
		<form method="POST">
			<input type="text" name="user" placeholder="login">
			<input type="password" name="password" placeholder="haslo">
			<input type="submit" value="zaloguj">
		</form>
		<p><a href="rejestracja.php">rejestracja</a></p>
	</div>
</body>
</html>