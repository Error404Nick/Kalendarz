<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: logowanie.php");
    exit();
}

if(isset($_GET['id'])){
    $conn = mysqli_connect("localhost", "root", "", "baza");
    
    $zalogowany_user = $_SESSION['user'];
    $user_query = mysqli_query($conn, "SELECT id FROM users WHERE user='$zalogowany_user'");
    $user_data = mysqli_fetch_assoc($user_query);
    $user_id = $user_data['id'];
    
    $task_id = (int)$_GET['id'];
    
    $stmt = mysqli_prepare($conn, "DELETE FROM tasks WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    mysqli_close($conn);
}

header("Location: index.php");
exit();
?>