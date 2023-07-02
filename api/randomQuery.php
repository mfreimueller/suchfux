<?php

require_once 'database.php';

$database = new Database();
$database->open();

$query = $database->getRandomSearchQuery();

header("Content-Type: application/json");
echo json_encode($query);

// ?>