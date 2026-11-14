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

$month = isset($_GET['month']) ? (int)$_GET['month'] : 11;
$year = isset($_GET['year']) ? (int)$_GET['year'] : 2026;

if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}

$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

$tasks_query = mysqli_query($conn, "SELECT id, task_date, content FROM tasks WHERE user_id='$user_id'");
$tasks = [];
while($row = mysqli_fetch_assoc($tasks_query)) {
    $tasks[$row['task_date']][] = [
        'id' => $row['id'],
        'content' => $row['content']
    ];
}

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
        <h1>Witaj, <?php echo htmlspecialchars($_SESSION['user']); ?></h1>
        <?php
            echo "<h2>$month-$year</h2>";
        ?>
    </div>

    <div class="container">
        <a href="index.php?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="nav-btn">←</a>
        <div class="kalendarz">
        <?php
            for($i = 1; $i <= $days; $i++) {
                $fullDate = sprintf('%04d-%02d-%02d', $year, $month, $i);
                echo '<div onclick="otworzFormularz(\'' . $fullDate . '\')">';
                echo '<strong>' . $i . '</strong>';
                
                if(isset($tasks[$fullDate])) {
                    echo '<div class="zadania-w-dniu">';
                    foreach($tasks[$fullDate] as $zadanie) {
                        echo '<div class="pojedyncze-zadanie" onclick="event.stopPropagation()">';
                        echo '<span>' . htmlspecialchars($zadanie['content']) . '</span>';
                        echo '<a href="usun_zadanie.php?id=' . $zadanie['id'] . '" class="btn-usun" onclick="return confirm(\'Na pewno usunąć?\')">X</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                
                echo '</div>';
            }
        ?>
        </div>
        <a href="index.php?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="nav-btn">→</a>
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