<?php

require_once 'database.php';

$database = new Database();
$database->open();

$query = $database->getRandomSearchQuery();

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");

echo json_encode($query);

// ?>