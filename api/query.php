<?php

/**
 * 
 */
class Query {

	public $query;
	public $suggestions;

	function __construct($query, $suggestions) {
		$this->query = $query;
		$this->suggestions = $suggestions;
	}

}

// ?>