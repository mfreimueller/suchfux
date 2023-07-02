<?php

require_once '../database.php';
require_once '../query.php';
require_once 'googleService.php';

$database = new Database();
$database->open();

$searchQueries = $database->getSearchQueries();

$googleService = new GoogleService();

foreach ($searchQueries as $query) {
	echo "Updating " . $query;
	
	$newQuery = $googleService->getMetaForQuery($query);
	$database->updateSearchQuery($newQuery);
}

echo "OK";

// ?>