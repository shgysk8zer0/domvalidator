<?php

namespace shgysk8zer0\DOMValidator;

use \shgysk8zer0\Core\Console as Console;

final class Form extends \ArrayObject implements \JsonSerializable
{
	private $_form;

	public function __construct(\DOMElement $form)
	{
		if ($form->tagName !== 'form') {
			throw new \InvalidArgumentException("Expected a <form> but got a <{$form->tagName}>.");
		}
		$this->_form = $form;

		foreach (Consts::INPUT_TAGS as $tag) {
			$els = $form->getElementsByTagName($tag);
			foreach ($els as $el) {
				$this[$el->getAttribute('name')] = new Filter($el);
			}
		}
	}

	/**
	 * [__invoke description]
	 * @param  Array  $inputs [description]
	 * @return [type]         [description]
	 * @see https://secure.php.net/manual/en/function.filter-var-array.php
	 */
	public function __invoke(Array $inputs)
	{
		$inputs = $this->_convertInputs($inputs);
		$filters = [];
		foreach ($this as $key => $filter) {
			$filters[$key] = $filter->getArrayCopy();
		}
		$filtered = filter_var_array($inputs, $filters);
		$filtered = array_filter($filtered);
		$filtered = urldecode(http_build_query($filtered));
		parse_str($filtered, $filtered);
		return $filtered;
	}

	public function __debugInfo()
	{
		return $this->getArrayCopy();
	}

	public function jsonSerialize()
	{
		return array_keys($this->getArrayCopy());
	}

	public static function loadFromFile($filename)
	{
		return static::loadFromHTML(file_get_contents($filename));
	}

	public static function loadFromHTML($html)
	{
		$dom = new \DOMDocument();
		$dom->loadHTML($html);
		$forms = $dom->getElementsByTagName('form');
		if ($forms->length === 1) {
			return new self($forms->item(0));
		} else {
			throw new \InvalidArgumentException("Expected a document with 1 <form>, got {$forms->length}.");
		}
	}

	private function _convertInputs(Array $inputs)
	{
		$parsed = urldecode(http_build_query($inputs));
		$values = [];
		foreach(explode('&', $parsed) as $input) {
			list($name, $value) = explode('=', $input);
			$values[$name] = $value;
		}

		return $values;
	}
}
