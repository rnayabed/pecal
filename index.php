<!DOCTYPE html>
<html>
<head>
<title>pecal</title>
</head>
<body>
<?php
session_start();

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
    $today = new DateTime();

    $first_of_month_date = new DateTime($today->format('Y-m-') . '1');
    $month_number = $today->format('m');
    $year_number = $today->format('Y');

    $total_days = $first_of_month_date->format('t');
    $total_weeks = ceil($total_days / 7);

    // 0 = Sunday
    // 6 = Saturday
    $weekday_number = $first_of_month_date->format('w');
    $day_number = 1;
    
    for($week = 0; $week < $total_weeks; $week++) {
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

            $events = $_SESSION['events'][$year_number][$month_number][$day_number] ?? null;
            if (isset($events) && count($events) > 0) {
                $text .= ' - ' . count($events);
                $color = 'green';
            } else {
                $color = 'blue';
            }

            if ($today->format('d') == $day_number) {
                $color = 'red';
            }

            echo "<td><a style=\"all:unset; cursor:pointer; color:$color;\" 
            href=\"events.php?y=$year_number&m=$month_number&d=$day_number\">$text</a></td>";
        }

        
        if ($week == $total_weeks - 1) {
            // Last week
            
            for (; $weekday_number < 7; $weekday_number++) {
                echo '<td></td>';
            }
        }

        $weekday_number = 0; // Reset

        echo '</tr>';
    }
}
?>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <caption><?php echo date('F Y');?></caption>
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
<span style="font-size:0.7rem; float:left;">Debayan Sutradhar - IT PCA2 Submission</span>
<a style="text-decoration:none; color: red; font-size:0.7rem; float:right" href="destroy.php">Reset everything</a>
</body>
</html>