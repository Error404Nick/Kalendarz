<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: logowanie.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "baza");

$zalogowany_user = $_SESSION['user'];
$user_query = mysqli_query($conn, "SELECT id FROM users WHERE user='$zalogowany_user'");
$user_data = mysqli_fetch_assoc($user_query);
$user_id = $user_data['id'];

$tasks_query = mysqli_query($conn, "SELECT * FROM tasks WHERE user_id='$user_id'");
$tasks = [];
while($row = mysqli_fetch_assoc($tasks_query)) {
    $tasks[$row['task_date']][] = $row['content'];
}

$month = 11;
$year = 2026;
$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalendarz</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div>
        <h1>Witaj</h1>
        <?php
            echo "<h2>$month-$year</h2>";
        ?>
    </div>

    <div class="container">
        <button>←</button>
        <div class="kalendarz">
        <?php
            for($i = 1; $i <= $days; $i++) {
                $fullDate = sprintf('%04d-%02d-%02d', $year, $month, $i);
                echo '<div onclick="otworzFormularz(\'' . $fullDate . '\')">';
                echo '<strong>' . $i . '</strong>';
                
                if(isset($tasks[$fullDate])) {
                    echo '<div class="zadania-w-dniu">';
                    foreach($tasks[$fullDate] as $tresc_zadania) {
                        echo '<p>' . htmlspecialchars($tresc_zadania) . '</p>';
                    }
                    echo '</div>';
                }
                
                echo '</div>';
            }
        ?>
        </div>
        <button>→</button>
    </div>
    <dialog id="modal-zadania">
        <form method="POST" action="dodaj_zadanie.php">
            <h3>Dodaj czynność</h3>
            
            <input type="hidden" name="data" id="wybrana-data-input">
            
            <p>Data: <strong id="pokaz-date"></strong></p>

            <label for="tresc">Treść zadania:</label><br>
            <textarea name="tresc" id="tresc" rows="4" required placeholder="Wpisz co masz do zrobienia..."></textarea>
            
            <div class="przyciski">
                <button type="submit">Zapisz</button>
                <button type="button" onclick="zamknijFormularz()">Anuluj</button>
            </div>
        </form>
    </dialog>
    <p><a href="wyloguj.php">Wyloguj się</a></p>
</body>
</html>