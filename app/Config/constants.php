<?php
define('PLAYER_TYPE_PLAYER', 1);
define('PLAYER_TYPE_GAME_MASTER', 2);

define('MAX_PLAYERS_PER_TEAM', 24);

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