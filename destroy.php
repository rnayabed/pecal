<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';

session_start();
unset($_SESSION['year']);
unset($_SESSION['month']);

delete_all_events();
header('Location: index.php');
?>