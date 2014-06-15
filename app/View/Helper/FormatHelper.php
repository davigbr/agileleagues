<?php

App::uses('AppHelper', 'View/Helper');

class FormatHelper extends AppHelper {

	public function date($sqlDateTime) {
		if (!$sqlDateTime) return '';
		if (strlen($sqlDateTime) === 10) {
			$sqlDateTime .= ' 00:00:00';
		}
		$dateTime = new DateTime($sqlDateTime);
		if (date('Y') === $dateTime->format('Y')) {
			return $dateTime->format('M jS (D)');
		} else {
			return $dateTime->format('M jS, Y');
		}
	}

	public function dateTime($sqlDateTime) {
		if (!$sqlDateTime) return '';
		if (strlen($sqlDateTime) === 10) {
			$sqlDateTime .= ' 00:00:00';
		}
		$dateTime = new DateTime($sqlDateTime);
		if (date('Y') === $dateTime->format('Y')) {
			return $dateTime->format('M jS (D) h:i A');
		} else {
			return $dateTime->format('M jS, Y');
		}
	}


	public function time($sqlDateTime) {
		if (!$sqlDateTime) return '';
		$dateTime = new DateTime($sqlDateTime);
		return $dateTime->format('h:i A');
	}

	public function trunc($text, $size = 20, $ellipsis = true) {
		if (strlen($text) > $size) {
			$text = substr($text, 0, $size-1);
			if ($ellipsis) {
				$text .= '&#8230;';
			}
		}
		return $text;
	}
}