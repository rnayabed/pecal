<?php
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;

$uri = 'mongodb://localhost:27017';
$client = new MongoDB\Client($uri);
$db = $client->pecal;

function get_events($date) {
    [$year, $month, $day] = $date;
    global $client;

    $events = $client->pecal->events->find([
        "date.year" => "$year",
        "date.month" => "$month",
        "date.day" => "$day"
    ]);

    return iterator_to_array($events);
}

function add_event($date, $title) {
    [$year, $month, $day] = $date;
    global $client;

    $client->pecal->events->insertOne([
        "date" => [
            "year" => "$year",
            "month" => "$month",
            "day" => "$day"
        ],
        "title" => "$title"
    ]);
}

function delete_event($id) {
    global $client;

    return $client->pecal->events->deleteOne([
        '_id' => new MongoDB\BSON\ObjectId($id)
    ]);
}

function delete_all_events() {
    global $client;
    $client->pecal->events->drop();
}