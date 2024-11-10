<!DOCTYPE html>
<html>
<head>
<title>pecal</title>
</head>
<body>
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';

$year = $_GET['y'] ?? null;
$month = $_GET['m'] ?? null;
$day = $_GET['d'] ?? null;

// Basic URL validation
$valid_date = false;
if ($year && filter_var($year, FILTER_VALIDATE_INT) &&
    $year >= 1970 && $year <= 9999 &&
    $month && filter_var($month, FILTER_VALIDATE_INT) &&
    $month >= 1 && $month <= 12 &&
    $day && filter_var($day, FILTER_VALIDATE_INT) &&
    $day >= 1) {
    
    // Month check
    if ((in_array($month, [1, 3, 5, 7, 8, 10, 12]) && $day <= 31) ||
        (in_array($month, [4, 6, 9, 11]) && $day <= 30)) {
        $valid_date = true;
    } else if ($month == 2) {
        /* Leap year check for february
        *  Criteria:
        *  - Must be divisible by 4
        *  - If divisible by 100, must also be divisible by 400 
        */ 
        if ($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0)) {
            $valid_date = $day <= 29;
        } else {
            $valid_date = $day <= 28;
        } 
    }
}

if (!$valid_date) {
    $date = new DateTime();
    header('Location: events.php?y=' . $date->format('Y') .
            '&m=' . $date->format('m') .
            '&d=' . $date->format('d') .
            '&error=date');
}

function redirect_regular($extra_args = ''){
    global $year, $month, $day;

    header("Location: events.php?y=$year&m=$month&d=$day" . $extra_args);
    exit();
}

$delete_id = $_GET['delete'] ?? null;
$new_event = $_GET['new_event'] ?? null;

if (isset($delete_id)) {
    // Delete event
    delete_event($delete_id);

    redirect_regular();
}
else if (isset($new_event)) {
    // Add new event
    if(strlen(trim($new_event)) == 0) {
        redirect_regular('&error=input');
    } 

    add_event([$year, $month, $day], $new_event);

    redirect_regular();
}

$date = new DateTime("$year-$month-$day");
echo "<h1>Events on " . $date->format('d-m-Y') . "</h1>";
?>

<a href="index.php" style="all:unset; cursor:pointer; color:blue;">Event calendar</a><br/></br><br/>

<form>
    <input type="hidden" name="y" value="<?php echo $year;?>">
    <input type="hidden" name="m" value="<?php echo $month;?>">
    <input type="hidden" name="d" value="<?php echo $day;?>">
    <label for="new_event">Add new event:</label>
    <input type="text" autocomplete="off" name="new_event"/>
    <button type="submit">Add</button>
</form>

<?php
$error = $_GET['error'] ?? null;

if ($error) {
    $error_text = match ($error) {
        'input'     => 'Invalid input',
        'date'      => 'Invalid date',
        default     => null
    };

    if ($error_text) {
        echo "<p style=\"color: red;\">$error_text</p>";
    }
}
?>

<br/>

<?php
$events = get_events([$year, $month, $day]);

if (count($events) > 0) {
    echo '<table cellspacing="10"><caption>Event list</caption><tbody>';

    for($i = 1; $i <= count($events); $i++) {
        $event = $events[$i - 1];
        $id = $event['_id'];
        $title = htmlspecialchars($event['title']);

        echo "<tr><td>$i</td><td width=\"75%\">$title</td><td><a href=\"events.php?y=$year&m=$month&d=$day&delete=$id\">Delete</a></tr>";
    }
    echo '</tbody></table>';
} else {
    echo 'No events created yet.';
}
?>
</body>
</html>