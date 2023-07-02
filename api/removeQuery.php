<?php

require_once 'database.php';

$query = $_GET["q"];

$database = new Database();
$database->open();

$database->removeSearchQuery($query);

if (key_exists("r", $_GET)) {
	header("location: " . $_GET["r"]);
}
// ?>