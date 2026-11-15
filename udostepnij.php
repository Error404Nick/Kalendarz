<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: logowanie.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $conn = mysqli_connect("localhost", "root", "", "baza");
    
    $zalogowany_user = $_SESSION['user'];
    $user_q = mysqli_query($conn, "SELECT id FROM users WHERE user='$zalogowany_user'");
    $user_data = mysqli_fetch_assoc($user_q);
    $owner_id = $user_data['id'];
    
    $docelowy_login = trim($_POST['docelowy_user']);
    $target_q = mysqli_query($conn, "SELECT id FROM users WHERE user='$docelowy_login'");
    
    if(mysqli_num_rows($target_q) > 0){
        $target_data = mysqli_fetch_assoc($target_q);
        $target_id = $target_data['id'];
        
        if($owner_id != $target_id){
            $check_q = mysqli_query($conn, "SELECT * FROM shared_calendars WHERE owner_id='$owner_id' AND shared_with_user_id='$target_id'");
            if(mysqli_num_rows($check_q) == 0){
                $stmt = mysqli_prepare($conn, "INSERT INTO shared_calendars (owner_id, shared_with_user_id, status) VALUES (?, ?, 'pending')");
                mysqli_stmt_bind_param($stmt, "ii", $owner_id, $target_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $msg = "Wysłano zaproszenie do użytkownika " . htmlspecialchars($docelowy_login);
            } else {
                $msg = "Zaproszenie do tej osoby zostało już wcześniej wysłane.";
            }
        } else {
            $msg = "Nie możesz udostępnić kalendarza samemu sobie.";
        }
    } else {
        $msg = "Taki użytkownik nie istnieje.";
    }
    
    mysqli_close($conn);
    header("Location: index.php?msg=" . urlencode($msg));
    exit();
}
?>