<?php

require_once 'query.php';

class GoogleService {

	function __construct() {

	}

	public function getMetaForQuery($query) {
		$raw = $this->getRawMetaDataForQuery($query);

		// doing a lil' butchering, because I hate working with XML.
		$metaData = json_decode(json_encode(simplexml_load_string($raw)), true);
		
		$suggestions = array();
		foreach ($metaData["CompleteSuggestion"] as $suggestion) {
			$data = $suggestion["suggestion"]["@attributes"]["data"];

			$suggestions[] = $data;
		}

		return new Query($query, $suggestions);
	}

	function getRawMetaDataForQuery($query) {
		$c = curl_init("https://suggestqueries.google.com/complete/search?output=toolbar&hl=de&q=" . str_replace(" ", "%20", $query));
		curl_setopt($c, CURLOPT_ENCODING, "UTF-8");
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	
		$result = curl_exec($c);
	
		if (curl_error($c)) {
			die(curl_error($c));
		}

		curl_close($c);

		return mb_convert_encoding($result, 'UTF-8', 'ISO-8859-1');
	}
}

// ?>