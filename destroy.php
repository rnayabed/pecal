<?php
session_start();
unset($_SESSION['events']);
header('Location: index.php');
?>