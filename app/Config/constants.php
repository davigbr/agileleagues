<?php
define('PLAYER_TYPE_PLAYER', 1);
define('PLAYER_TYPE_GAME_MASTER', 2);

define('EVENT_TYPE_MISSION', 1);
define('EVENT_TYPE_CHALLENGE', 2);
define('EVENT_LEVEL_REQUIRED_MISSION', 10);
define('EVENT_LEVEL_REQUIRED_CHALLENGE', 20);
define('EVENT_JOIN_XP', 5);

define('MAX_PLAYERS_PER_TEAM', 24);

define('PAIR_XP_MULTIPLIER', 1.2);
define('ACCEPTANCE_XP_MULTIPLIER', 0.1); //10% XP
define('REJECTION_XP_BONUS', 1); //+1XP

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