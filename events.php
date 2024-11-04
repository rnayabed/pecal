<!DOCTYPE html>
<html>
<head>
<title>pecal</title>
</head>
<body>
<?php
session_start();

$year = $_GET['y'];
$month = $_GET['m'];
$day = $_GET['d'];

function redirect_regular($extra_args = ''){
    global $year;
    global $month;
    global $day;

    header("Location: events.php?y=$year&m=$month&d=$day" . $extra_args);
    exit();
}


$delete_id = $_GET['delete'] ?? null;
$new_event = $_GET['new_event'] ?? null;

if (isset($delete_id)) {
    // Delete event
    unset($_SESSION['events'][$year][$month][$day][$delete_id]);

    redirect_regular();
}
else if (isset($new_event)) {
    // Add new event

    if(strlen(trim($new_event)) == 0) {
        redirect_regular('&error=1');
    } 

    $id = mt_rand();
    $_SESSION['events'][$year][$month][$day][$id] = $new_event;

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
if (isset($_GET['error'])) {
    echo '<p style="color: red;">Invalid input</p>';
}
?>

<br/>

<?php
$events = $_SESSION['events'][$year][$month][$day] ?? null;

if (isset($events) && count($events) > 0) {
    $ids = array_keys($events);

    echo '<table cellspacing="10"><caption>Event list</caption><tbody>';
    for($i = 1; $i <= count($ids); $i++) {
        $id = $ids[$i - 1];
        $event = htmlspecialchars($events[$id]);

        echo "<tr><td>$i</td><td width=\"75%\">$event</td><td><a href=\"events.php?y=$year&m=$month&d=$day&delete=$id\">Delete</a></tr>";
    }
    echo '</tbody></table>';
} else {
    echo 'No events created yet.';
}
?>

</body>
</html>