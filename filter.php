<?php

namespace shgysk8zer0\DOMValidator;

final class Filter extends \ArrayObject
{
	use Traits\Filters;
	protected $_input;

	public function __construct(\DOMElement $input)
	{
		parent::__construct([
			'filter' => FILTER_CALLBACK,
			'options' => [$this, 'validate']
		]);
		$this->_input = $input;
		//$this->filter(FILTER_CALLBACK)->options($this, 'validate');
	}

	public function __set($prop, $value)
	{
		$this->_input->setAttribute($prop, $value);
	}

	public function __get($prop)
	{
		return $this->_input->getAttribute($prop);
	}

	public function __isset($prop)
	{
		return $this->_input->hasAttribute($prop);
	}

	public function __call($prop, Array $args)
	{
		if (!empty($args)) {
			$this[$prop] = count($args) === 1 ? $args[0] : $args;
		}
		return $this;
	}

	public function __invoke($value)
	{
		echo $value . PHP_EOL;
		if (!empty($value)) {
			$this->_input->setAttribute('value', $value);
		}
		echo $this->_input->ownerDocument->saveHTML($this->_input) . PHP_EOL;
		return $value;
	}
}
