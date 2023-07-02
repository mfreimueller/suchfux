<?php

class Config {

	public $databasePath = "db.sqlite";

	private static $instance = NULL;

	static function get(): Config {
		if (self::$instance == NULL) {
			self::$instance = new Config();
		}

		return self::$instance;
	}

}

// ?>