<?php

require_once 'database.php';
require_once 'googleService.php';

$queryToAdd = str_replace("%20", " ", $_GET["q"]);
$googleService = new GoogleService();
$query = $googleService->getMetaForQuery($queryToAdd);

$database = new Database();
$database->open();

$database->addSearchQuery($query);

if (key_exists("r", $_GET)) {
	header("location: " . $_GET["r"]);
}
// ?>