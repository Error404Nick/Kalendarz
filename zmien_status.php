<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: logowanie.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['action'])){
    $conn = mysqli_connect("localhost", "root", "", "baza");
    
    $zalogowany_user = $_SESSION['user'];
    $user_q = mysqli_query($conn, "SELECT id FROM users WHERE user='$zalogowany_user'");
    $user_data = mysqli_fetch_assoc($user_q);
    $user_id = $user_data['id'];
    
    $rel_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if($action == 'accept'){
        $stmt = mysqli_prepare($conn, "UPDATE shared_calendars SET status = 'accepted' WHERE id = ? AND shared_with_user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $rel_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif($action == 'reject'){
        $stmt = mysqli_prepare($conn, "DELETE FROM shared_calendars WHERE id = ? AND shared_with_user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $rel_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
}

header("Location: index.php");
exit();
?>