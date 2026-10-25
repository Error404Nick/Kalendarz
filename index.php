<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalendarz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div>
        <h1>Witaj</h1>
    <?php
        $month = 10;
        $year = 2026;
        $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        echo"<h2>$month-$year</h2>";
    ?>
    </div>

    <div class="container">
        <button>←</button>
    <div class="kalendarz">
    <?php
    $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    for($i=1; $i<=$days; $i++){
        echo '<div onclick="alert(\'Kliknięto!\')" id="' . $i . '-' . $month . '-' . $year . '">' . $i . '</div>';
    }
    ?>
    </div>
    <button>→</button>
    </div>
</body>
</html>