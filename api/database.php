<?php

require_once 'config.php';
require_once 'query.php';

class Database {

	private $db;

	function __construct() {

	}

	public function open() {
		$this->db = new SQLite3(Config::get()->databasePath);
		$this->createStructure();
	}

	function createStructure() {
		$this->db-> exec(
			"CREATE TABLE IF NOT EXISTS SearchQueries(query TEXT PRIMARY KEY NOT NULL)"
		);

		$this->db-> exec(
			"CREATE TABLE IF NOT EXISTS SearchQuerySuggestions(
				query TEXT NOT NULL,
				suggestion TEXT NON NULL,
				`order` INT NOT NULL,
				PRIMARY KEY (query, suggestion),
				FOREIGN KEY (query) REFERENCES searchQueries(query))"
		);
	}

	function addSearchQuery($query) {
		$this->db->exec("INSERT INTO SearchQueries (query) VALUES ('" . urlencode($query->query) . "')");

		for ($idx = 0; $idx < count($query->suggestions); $idx++) {
			$suggestion = $query->suggestions[$idx];
			$this->db->exec("INSERT INTO SearchQuerySuggestions (query, suggestion, `order`) VALUES ('" . urlencode($query->query) . "', '" . urlencode($suggestion) . "', $idx)");
		}
	}

	function removeSearchQuery($query) {
		echo "DELETE FROM SearchQuerySuggestions WHERE query = '" . urlencode($query) . "'";
		$this->db->exec("DELETE FROM SearchQuerySuggestions WHERE query = '" . urlencode($query) . "'");
		$this->db->exec("DELETE FROM SearchQueries WHERE query = '" . urlencode($query) . "'");
	}

	public function getSearchQueries() {
		$results = $this->db->query("SELECT query FROM SearchQueries ORDER BY query");

		$searchQueries = array();
		while ($row = $results->fetchArray()) {
			$query = $row["query"];
			$searchQueries[] = urldecode($query);
		}

		return $searchQueries;
	}

	public function getSearchQuerySuggestions($query) {
		$results = $this->db->query("SELECT suggestion, `order` FROM searchQuerySuggestions WHERE query = '" . urlencode($query) . "'");

		$suggestions = range(0, 9);
		while ($row = $results->fetchArray()) {
			$suggestion = $row["suggestion"];
			$order = intval($row["order"]);
			$suggestions[$order] = urldecode($suggestion);
		}

		return new Query($query, $suggestions);
	}

	public function getRandomSearchQuery() {
		$results = $this->db->query("SELECT query, suggestion, `order` FROM searchQuerySuggestions WHERE query = (SELECT query FROM SearchQueries ORDER BY RANDOM() LIMIT 1)");

		$query = NULL;
		$suggestions = range(0, 9); // create dummy array of maximum size 10
		while ($row = $results->fetchArray()) {
			$query = urldecode($row["query"]);

			$order = intval($row["order"]);
			$suggestion = $row["suggestion"];
			$suggestions[$order] = urldecode($suggestion);
		}

		return new Query($query, $suggestions);
	}

}

// ?>