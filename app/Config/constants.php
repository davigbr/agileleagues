<?php
define('PLAYER_TYPE_DEVELOPER', 1);
define('PLAYER_TYPE_SCRUMMASTER', 2);
define('PLAYER_TYPE_PRODUCT_OWNER', 3);

define('EVENT_TYPE_MISSION', 1);
define('EVENT_TYPE_CHALLENGE', 2);
define('EVENT_LEVEL_REQUIRED_MISSION', 10);
define('EVENT_LEVEL_REQUIRED_CHALLENGE', 20);
define('EVENT_JOIN_XP', 5);

define('MAX_DEVELOPERS_PER_TEAM', 24);

function array_clear(&$array) {
	foreach ($array as $key => $value) {
		unset($array[$key]);
	}
}

function array_replace_keys(&$array, $callback) {
	$copy = $array;
	array_clear($array);
	foreach ($copy as $value) {
		$array[$callback($value)] = $value;
	}
	ksort($array);
	return $array;
}

class ModelException extends Exception {
	
}