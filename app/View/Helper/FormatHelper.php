<?php

App::uses('AppHelper', 'View/Helper');
App::uses('TimeHelper', 'View/Helper');

class FormatHelper extends AppHelper {

	private $Time = null;

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->Time = new TimeHelper($View, $settings);
		if (isset($settings['timezone'])) {
			$this->timezone = $settings['timezone'];
		} else {
			$this->timezone = 'UTC';
		}
	}

	public function date($sqlDateTime) {
		if (!$sqlDateTime || $sqlDateTime === '0000-00-00' || $sqlDateTime === '0000-00-00 00:00:00') return '';
		if (strlen($sqlDateTime) === 10) {
			$sqlDateTime .= ' 00:00:00';
		}
		$dateTime = new DateTime($sqlDateTime);
		if (date('Y') === $dateTime->format('Y')) {
			return $this->Time->format('M jS (D)', $sqlDateTime, null, $this->timezone);
		} else {
			return $this->Time->format('M jS, Y', $sqlDateTime, null, $this->timezone);
		}
	}

	public function dateTime($sqlDateTime) {
		if (!$sqlDateTime) return '';
		if (strlen($sqlDateTime) === 10) {
			$sqlDateTime .= ' 00:00:00';
		}
		$dateTime = new DateTime($sqlDateTime);
		if (date('Y') === $dateTime->format('Y')) {
			return $this->Time->format('M jS (D) h:i A', $sqlDateTime, null, $this->timezone);
		} else {
			return $this->Time->format('M jS, Y', $sqlDateTime, null, $this->timezone);
		}
	}


	public function time($sqlDateTime) {
		if (!$sqlDateTime) return '';
		return $this->Time->format('h:i A', $sqlDateTime, null, $this->timezone);
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