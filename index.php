<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: logowanie.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "baza");

$zalogowany_user = $_SESSION['user'];
$user_query = mysqli_query($conn, "SELECT id FROM users WHERE user='$zalogowany_user'");

if(mysqli_num_rows($user_query) == 0) {
    session_destroy();
    header("Location: logowanie.php");
    exit();
}

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

$tasks_query = mysqli_query($conn, "
    SELECT id, task_date, content FROM tasks WHERE user_id = '$user_id'
    UNION
    SELECT t.id, t.task_date, t.content FROM tasks t
    JOIN shared_calendars s ON t.user_id = s.owner_id
    WHERE s.shared_with_user_id = '$user_id' AND s.status = 'accepted'
");
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
                        echo '<a href="usun_zadanie.php?id=' . $zadanie['id'] . '" class="btn-usun" onclick="return confirm(\'Na pewno usunąć?\')">❌</a>';
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

    <div style="margin-top: 20px; background: white; padding: 15px; border-radius: 6px; width: 560px; box-sizing: border-box;">
        <h3>Udostępnij swój kalendarz</h3>
        <?php if(isset($_GET['msg'])): ?>
            <p style="color: green; font-size: 14px;"><?php echo htmlspecialchars($_GET['msg']); ?></p>
        <?php endif; ?>
        <form action="udostepnij.php" method="POST">
            <input type="text" name="docelowy_user" placeholder="Login użytkownika" required style="padding: 6px; width: 60%;">
            <button type="submit" style="padding: 6px 12px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">Wyślij zaproszenie</button>
        </form>

        <?php
        $pending_q = mysqli_query($conn, "
            SELECT sc.id, u.user as owner_name 
            FROM shared_calendars sc 
            JOIN users u ON sc.owner_id = u.id 
            WHERE sc.shared_with_user_id = '$user_id' AND sc.status = 'pending'
        ");
        
        if(mysqli_num_rows($pending_q) > 0):
        ?>
            <h4 style="margin-top: 15px; color: #e67e22;">Oczekujące zaproszenia od innych:</h4>
            <ul>
                <?php while($req = mysqli_fetch_assoc($pending_q)): ?>
                    <li>
                        Użytkownik <strong><?php echo htmlspecialchars($req['owner_name']); ?></strong> chce udostępnić Ci kalendarz. 
                        <a href="zmien_status.php?id=<?php echo $req['id']; ?>&action=accept" style="color: green; font-weight: bold; margin-left: 5px;">[Akceptuj]</a>
                        <a href="zmien_status.php?id=<?php echo $req['id']; ?>&action=reject" style="color: red; font-weight: bold; margin-left: 5px;">[Odrzuć]</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
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