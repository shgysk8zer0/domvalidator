<?php
namespace shgysk8zer0\DOMValidator;

final class Pattern
{
	private $_pattern = '';
	public function __construct($pattern)
	{
		$this->_pattern = '/^' . ltrim(rtrim($pattern, '$'), '^') . '$/';
	}

	public function __toString()
	{
		return $this->_pattern;
	}

	public function __invoke($subject)
	{
		return preg_match($this, $subject);
	}
}
