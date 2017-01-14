<?php

namespace shgysk8zer0\DOMValidator\Traits;

use \shgysk8zer0\DOMValidator\Pattern as Pattern;
use \shgysk8zer0\DOMValidator\Consts as Consts;
use \shgysk8zer0\Core\Console as Console;

/**
 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input
 */
trait Filters
{
	final public function validate($value)
	{
		if (!isset($this->type)) {
			$this->type = 'text';
		} else {
			$this->type = strtolower($this->type);
		}
		switch($this->type) {
			case 'text':
			case 'search':
			case 'password':
				$this->filterText($value);
				break;
			case 'hidden':
				if ($value !== $this->value) {
					$value = false;
				}
				break;

			case 'email':
				$this->filterEmail($value);
				break;
			case 'url':
				$this->filterURL($value);
				break;

			case 'number':
			case 'range':
				$this->filterNumber($value);
				break;

			case 'date':
				$this->filterDate($value);
				break;

			case 'time':
				$this->filterTime($value);
				break;

			case 'datetime':
			case 'datetime-local':
				$this->filterDateTime($value);
				break;

			case 'week':
				break;

			case 'month':
				break;

			case 'color':
				$this->filterColor($value);
				break;

			default:
				$this->type = 'text';
				$value = $this->validate($value);
		}
		return $value;
	}

	final protected function filterVar(&$value, $filter = FILTER_DEFAULT)
	{
		if (!filter_var($value, $filter)) {
			$value = false;
		}
	}

	final protected function filterText(&$value)
	{
		if ($value) {
			$this->checkReadOnly($value);
		}
		if ($value) {
			$this->checkPattern($value);
		}
		if ($value) {
			$this->checkLength($value);
		}
	}

	final protected function filterNumber(&$value)
	{
		$value = $this->parseNumber($value);
		$step = $max = $min = null;
		if (is_null($value)) {
			return;
		}
		if (isset($this->step)) {
			$step = $this->parseNumber($this->step);
			if(!isset($step) or $step <= 0) {
				throw new \Exception("Invalid step attribute: [{$this->step}]");
			}
		}

		if (isset($this->min)) {
			$min = $this->parseNumber($this->min);
		}

		if (isset($this->max)) {
			$max = $this->parseNumber($this->max);
		}
		if (is_int($step) and ! is_int($value)) {
			$value = false;
		}

		if (is_int($step) and $value % $step !== 0) {
			$value = false;
		} elseif (is_float($step)) {
			// Comparing floats is complicated in PHP due to rounding/precision
			if (ceil($value / $step) !== floor($value / $step)) {
				$value = false;
			}
		}

		if (isset($max) and $value > $max) {
			$value = false;
		}

		if (isset($min) and $value < $min) {
			$value = false;
		}
	}

	final protected function filterDate(&$value)
	{
		$pattern = new Pattern(Consts::PATTERNS['date']);
		if ($pattern($value)) {
			$date = new \DateTime($value);
			if (isset($this->min) and $pattern($this->min)) {
				$min = new \DateTime($this->min);
			}
			if (isset($this->max) and $pattern($this->max)) {
				$max = new \DateTime($this->max);
			}
			if ((isset($min) and $date < $min) or (isset($max) and $date > $max)) {
				$value = false;
			} else {
				$value = $date->format('Y-m-d');
			}
		} else {
			$value = false;
		}
	}

	final protected function filterTime(&$value)
	{
		try {
			$pattern = new Pattern(Consts::PATTERNS['time']);
			if ($pattern($value)) {
				$time = new \DateTime($value);
				$value = $time->format('H:i:sP');
				$min = $max = null;
				if (isset($this->min) and $pattern($this->min)) {
					$min = new \DateTime($this->min);
				}
				if (isset($this->max) and $pattern($this->max)) {
					$max = new \DateTime($this->max);
				}
				if ((isset($min) and $time < $min) or (isset($max) and $time > $max)) {
					$value = false;
				}
			} else {
				Console::error("$value is not in a valid time format");
				$value = false;
			}
		} catch(\Exception $e) {
			Console::error($e);
		}
	}
	final protected function filterDateTime(&$value)
	{
		try {
			$pattern = new Pattern(Consts::PATTERNS['datetime']);
			if ($pattern($value)) {
				$date = new \DateTime($value);
				$value = $date->format(\DateTime::W3C);
				$min = $max = null;

				if (isset($this->min) and $pattern($this->min)) {
					$min = new \DateTime($this->min);
				}
				if (isset($this->max) and $pattern($this->max)) {
					$max = new \DateTime($this->max);
				}
				if ((isset($min) and $date < $min) or (isset($max) and $date > $max)) {
					$value = false;
				}
			} else {
				$value = false;
			}
		} catch (\Exception $e) {
			$value = false;
		}
	}

	final protected function filterEmail(&$value)
	{
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			$value = false;
		}
		if ($value) {
			$this->filterText($value);
		}
	}

	final protected function filterURL(&$value)
	{
		if (!filter_var($value, FILTER_VALIDATE_URL)) {
			$value = false;
		}
		$this->filterText($value);
	}

	final protected function filterColor(&$value)
	{
		$pattern = new Pattern(Consts::PATTERNS['color']);
		if (!$pattern($value)) {
			$value = false;
		}
	}

	final protected function checkReadOnly(&$value)
	{
		if (isset($this->readonly) and $value != $this->value) {
			$value = false;
		}
	}

	final protected function checkPattern(&$value)
	{
		if (isset($this->pattern)) {
			$pattern = new Pattern($this->pattern);
			if (!$pattern($value)) {
				$value = false;
			}
		}
	}

	final protected function checkLength(&$value)
	{
		if (isset($this->minlength) and strlen($value) < intval($this->minlength)) {
			$value = false;
			return;
		}
		if (isset($this->maxlength) and strlen($value) > intval($this->maxlength)) {
			$value = false;
		}
	}

	final protected function parseNumber($num)
	{
		if (filter_var($num, FILTER_VALIDATE_INT)) {
			$num = intval($num);
		} elseif (filter_var($num, FILTER_VALIDATE_FLOAT)) {
			$num = floatval($num);
		} elseif($num == '0' or preg_match('/^0*\.0+/$', $num)) {
			$num = 0;
		} else {
			$num = null;
		}
		return $num;
	}
}
