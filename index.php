<!DOCTYPE html>
<html>
<head>
<title>pecal</title>
</head>
<body>
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';

session_start();

$today = new DateTime();
$today_year = $today->format('Y');
$today_month = $today->format('m');
$today_day = $today->format('d');

$session_year = $_SESSION['year'] ?? null;
$session_month = $_SESSION['month'] ?? null;
    
if (!$session_year || !$session_month) {
    $session_year = $today_year;
    $session_month = $today_month;
}

$shift_month = $_GET['shift_month'] ?? null;

if ($shift_month == 'down') {
    if ($session_month == 1) {
        if ($session_year > 1970) {
            $session_year--;
            $session_month = 12;
        }
    } else {
        $session_month--;
    }

    header('Location: index.php');
} else if ($shift_month == 'up') {
    if ($session_month == 12) {
        if ($session_year < 9999) {
            $session_year++;
            $session_month = 1;
        }
    } else {
        $session_month++;
    }

    header('Location: index.php');
}

$_SESSION['year'] = $session_year;
$_SESSION['month'] = $session_month;

$calendar_date = new DateTime("$session_year-$session_month-1");

function generate_weekdays_table_header() {
    $weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $size = count($weekdays);
            
    echo '<tr>';
    foreach ($weekdays as $day) {
        echo "<th width=\"$size%\">$day</th>";
    }
    echo '</tr>';
}

function generate_calendar_cells() {
    global $calendar_date, 
        $session_year, $session_month,
        $today_year, $today_month, $today_day;

    $total_days = $calendar_date->format('t');
    $total_weeks = ceil($total_days / 7);

    // 0 = Sunday
    // 6 = Saturday
    $weekday_number = $calendar_date->format('w');
    $day_number = 1;

    error_log("Month $session_year - $session_month\n");
    error_log("Total days: $total_days\n");
    error_log("Total weeks: $total_weeks\n");

    
    for($week = 0; $day_number <= $total_days; $week++) {
        // Row
        echo '<tr>';
        if ($week == 0) {
            // First week
            
            for ($i = 0; $i < $weekday_number; $i++) {
                echo '<td></td>';
            }
        }

        // Render cells
        for (;$weekday_number < 7 && $day_number <= $total_days; $weekday_number++, $day_number++) {

            $text = $day_number;

            $events = get_events([$session_year, $session_month, $day_number]);
            if (count($events) > 0) {
                $text .= ' - ' . count($events);
                $color = 'green';
            } else {
                $color = 'blue';
            }

            if ($session_year == $today_year && 
                $session_month == $today_month && 
                $day_number == $today_day) {
                $color = 'red';
            }

            echo "<td><a style=\"text-decoration:none; color:$color;\" 
            href=\"events.php?y=$session_year&m=$session_month&d=$day_number\">$text</a></td>";
        }

        
        if ($day_number > $total_days) {
            // Last day surpassed
            
            for (; $weekday_number < 7; $weekday_number++) {
                echo '<td></td>';
            }
        }

        $weekday_number = 0; // Reset

        echo '</tr>';
    }
}
?>

<a href="index.php?shift_month=down" style="text-decoration:none; color:blue; font-size:2rem; float:left;">&lt</a>
<a href="index.php?shift_month=up" style="text-decoration:none; color:blue; font-size:2rem; float:right;">&gt</a>

<center style="text-decoration:none; font-size:2rem;">
<?php
global $calendar_date;
echo $calendar_date->format('F Y'); 
?>
</center>
    

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <?php
        generate_weekdays_table_header();
        ?>
    </thead>
    <tbody>
        <?php
        generate_calendar_cells();
        ?>
    </tbody>
</table>

<br/>
<span style="font-size:0.7rem; float:left;">Debayan Sutradhar - IT PCA2 Submission - Version 2</span>
<a style="text-decoration:none; color: red; font-size:0.7rem; float:right" href="destroy.php">Reset everything</a>
</body>
</html>