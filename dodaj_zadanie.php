<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: logowanie.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $conn = mysqli_connect("localhost", "root", "", "baza");
    
    $zalogowany_user = $_SESSION['user'];
    $user_query = mysqli_query($conn, "SELECT id FROM users WHERE user='$zalogowany_user'");
    $user_data = mysqli_fetch_assoc($user_query);
    $user_id = $user_data['id'];
    
    $data = $_POST['data'] ?? '';
    $tresc = $_POST['tresc'] ?? '';
    
    if(!empty($data) && !empty($tresc)){
        $stmt = mysqli_prepare($conn, "INSERT INTO tasks (user_id, task_date, content) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $data, $tresc);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    header("Location: index.php");
    exit();
}
?>